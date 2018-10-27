<?php
namespace App\Service;

class TradeMaster
{
    public function getAllCompanyTickers() : array
    {
        return [
            'Apple' => 'AAPL',
            'Google' => 'GOOG'
        ];
    }

    public function getQuotationByCompanyTicker(string $ticker) : float
    {
        return 100;
    }
}
