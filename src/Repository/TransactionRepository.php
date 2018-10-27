<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Service\UserLoader;

class TransactionRepository extends ServiceEntityRepository
{
    private $userLoader;

    public function __construct(RegistryInterface $registry, UserLoader $userLoader)
    {
        parent::__construct($registry, Transaction::class);
        $this->userLoader = $userLoader;
    }

    public function createPortfolioByCurrentUser() : array
    {
        return array_reduce(
            $this->findByUser($this->userLoader->getUser()),
            function (array $companies, Transaction $transaction) : array {
                $ticker = $transaction->getCompanyTicker();

                if (!isset($companies[$ticker])) {
                    $companies[$ticker] = ['ticker' => $ticker, 'sharesCount' => 0];
                }

                $sharesCount = &$companies[$ticker]['sharesCount'];
                $boughtSharesCount = $transaction->getSharesCount();

                if ($transaction->isBuy()) {
                    $sharesCount += $boughtSharesCount;
                } else {
                    $sharesCount -= $boughtSharesCount;
                }

                return $companies;
            },
            []
        );
    }

    public function getTotalSharesCountByCurrentUserAndTicker(string $ticker) : int
    {
        $transactions = $this->findBy(['user' => $this->userLoader->getUser(), 'companyTicker' => $ticker]);

        return array_reduce($transactions, function (int $totalSharesCount, Transaction $transaction) : int {
            $sharesCount=$transaction->getSharesCount();

            return $transaction->isBuy() ? $totalSharesCount + $sharesCount : $totalSharesCount - $sharesCount;
        }, 0);
    }
}
