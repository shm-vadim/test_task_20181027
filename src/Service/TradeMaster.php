<?php
namespace App\Service;

use App\Utils\Cache\LocalCache;
use DPRMC\IEXTrading\IEXTrading;
use Illuminate\Support\Collection;
use DPRMC\IEXTrading\Responses\StockChartDay as Day;

class TradeMaster
{
    private $localCache;

    public function __construct(LocalCache $localCache)
    {
        $this->localCache = $localCache;
    }

    public function getAllCompaniesTickers() : array
    {
        return [
            'Apple' => 'AAPL',
            'Google' => 'GOOG'
        ];
    }

    public function getQuotationByTicker(string $ticker, \DateTimeInterface $ago = null) : float
    {
        $chart = $this->localCache->get(['chart[ticker=%s, interval=2y]', $ticker], function () use ($ticker) : Collection {
            return IEXTrading::stockChart($ticker, '2y')->data;
        });

        $day = $chart->last();

        if ($ago) {
            $day = array_reduce($chart->toArray(), function (? Day $maxCloseDay, Day $day) use ($ago) : Day {
                if (!$maxCloseDay) {
                    return $day;
                }

                $absInterval = function (Day $day) use ($ago) : int {
                    return abs($ago->getTimestamp() - \DateTime::createFromFormat('Y-m-d', $day->date)->getTimestamp());
                };

                return $absInterval($maxCloseDay) < $absInterval($day) ? $maxCloseDay : $day;
            });
        }

        return $day->vwap;
    }

    public function getCompanyNameByTicker(string $ticker) : string
    {
        return array_search($ticker, $this->getAllCompaniesTickers());
    }
}
