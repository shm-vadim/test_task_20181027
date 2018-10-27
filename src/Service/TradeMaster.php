<?php
namespace App\Service;

class TradeMaster
{
    public function getAllCompaniesTickers() : array
    {
        return [
            'Apple' => 'AAPL',
            'Google' => 'GOOG'
        ];
    }

    public function getQuotationByTicker(string $ticker, \DateTimeInterface $ago = null) : float
    {
        return 100;
    }

    public function getCompanyNameByTicker(string $ticker) : string
    {
        return array_search($ticker, $this->getAllCompaniesTickers());
    }
}
