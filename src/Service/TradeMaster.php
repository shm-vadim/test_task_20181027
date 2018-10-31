<?php

namespace App\Service;

use App\Utils\Cache\LocalCache;
use DPRMC\IEXTrading\IEXTrading;
use DPRMC\IEXTrading\Responses\StockChartDay as Day;
use Illuminate\Support\Collection;

class TradeMaster
{
    private $localCache;

    public function __construct(LocalCache $localCache)
    {
        $this->localCache = $localCache;
    }

    public function getAllCompaniesTickers(): array
    {
        return [
            'Adobe' => 'ADBE',
            'Amazon' => 'AMZN ',
            'Apple' => 'AAPL',
            'Google' => 'GOOG',
            'Microsoft' => 'MCFT',
        ];
    }

    public function getQuotationByTicker(string $ticker, \DateTimeInterface $ago = null): float
    {
        $chart = $this->localCache->get(['chart[ticker=%s, interval=2y]', $ticker], function () use ($ticker): Collection {
            return IEXTrading::stockChart($ticker, '2y')->data;
        });

        $day = $chart->last();

        if ($ago) {
            $day = array_reduce($chart->toArray(), function (? Day $maxCloseDay, Day $day) use ($ago): Day {
                if (!$maxCloseDay) {
                    return $day;
                }

                $absInterval = function (Day $day) use ($ago): int {
                    return abs($ago->getTimestamp() - \DateTime::createFromFormat('Y-m-d', $day->date)->getTimestamp());
                };

                return $absInterval($maxCloseDay) < $absInterval($day) ? $maxCloseDay : $day;
            });
        }

        return $day->vwap;
    }

    public function getCompanyNameByTicker(string $ticker): string
    {
        return array_search($ticker, $this->getAllCompaniesTickers(), true);
    }

    public function getDividentsByTickerAndYear(string $ticker, \DateTimeInterface $dt): float
    {
        $dividends = $this->localCache->get(['dividends[ticker=%s, ago=2y', $ticker], function () use ($ticker): array {
            $response = \file_get_contents(sprintf(
                'https://api.iextrading.com/1.0/stock/%s/dividends/2y',
                urlencode($ticker)
            ));

            return dump(json_decode($response, true));
        });

        $dividendsByYear = array_reduce($dividends, function (float $dividends, array $record) use ($dt): float {
            return \DateTime::createFromFormat('Y-m-d', $record['paymentDate'])->format('Y') === $dt->format('Y')
                ? $dividends + $record['amount']
                : $dividends;
        }, 0);

        return $dividendsByYear;
    }
}
