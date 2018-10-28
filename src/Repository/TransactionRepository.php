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
return $this->getEntityManager()
->createQuery('select t.companyTicker as ticker, sum(t.sharesCount) as sharesCount from App:Transaction t
where t.user = :user
group by t.companyTicker')
->setParameters(['user'=>$this->userLoader->getUser()])
->getResult();
    }

    public function getTotalSharesCountByCurrentUserAndTicker(string $ticker) : int
    {
        return $this->getEntityManager()
            ->createQuery('select sum(t.sharesCount) as s from App:Transaction t
        where t.user = :user and t.companyTicker = :ticker')
            ->setParameters(['user' => $this->userLoader->getUser(), 'ticker' => $ticker])
            ->GetOneOrNullResult()['s'];
    }
}
