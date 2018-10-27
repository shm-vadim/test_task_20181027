<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addTime;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $companyTicker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $sharesCount = 10;

    /**
     * @ORM\Column(type="float")
     */
    private $money;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBuy = true;

    public function __construct()
    {
        $this->addTime = new \DateTime();
    }
    public function getId() : ? int
    {
        return $this->id;
    }

    public function getAddTime() : ? \DateTimeInterface
    {
        return $this->addTime;
    }

    public function setAddTime(\DateTimeInterface $addTime) : self
    {
        $this->addTime = $addTime;

        return $this;
    }

    public function getCompanyTicker() : ? string
    {
        return $this->companyTicker;
    }

    public function setCompanyTicker(string $companyTicker) : self
    {
        $this->companyTicker = $companyTicker;

        return $this;
    }

    public function getUser() : ? User
    {
        return $this->user;
    }

    public function setUser(? User $user) : self
    {
        $this->user = $user;

        return $this;
    }

    public function getSharesCount() : ? int
    {
        return $this->sharesCount;
    }

    public function setSharesCount(int $sharesCount) : self
    {
        $this->sharesCount = $sharesCount;

        return $this;
    }

    public function getMoney() : ? float
    {
        return $this->money;
    }

    public function setMoney(float $money) : self
    {
        $this->money = $money;

        return $this;
    }

    public function getIsBuy() : ? bool
    {
        return $this->isBuy;
    }

    public function setIsBuy(bool $isBuy) : self
    {
        $this->isBuy = $isBuy;

        return $this;
    }
}
