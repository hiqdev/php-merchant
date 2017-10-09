<?php

namespace hiqdev\php\merchant\response;

use Money\Currency;
use Money\Money;

/**
 * Class CompletePurchaseResponse
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CompletePurchaseResponse
{
    /**
     * @var bool
     */
    protected $isSuccessful;
    /**
     * @var Currency
     */
    protected $currency;
    /**
     * @var Money
     */
    protected $amount;
    /**
     * @var Money
     */
    protected $fee;
    /**
     * @var \DateTime
     */
    protected $time;
    /**
     * @var string
     */
    protected $transactionReference;
    /**
     * @var string
     */
    protected $transactionId;
    /**
     * @var string
     */
    protected $payer;

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return CompletePurchaseResponse
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @param Money $amount
     * @return CompletePurchaseResponse
     */
    public function setAmount(Money $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Money
     */
    public function getFee()
    {
        if ($this->fee !== null) {
            return $this->fee;
        }

        return new Money(0, $this->getCurrency());
    }

    /**
     * @param Money $fee
     * @return CompletePurchaseResponse
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return CompletePurchaseResponse
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->transactionReference;
    }

    /**
     * @param string $transactionReference
     * @return CompletePurchaseResponse
     */
    public function setTransactionReference($transactionReference)
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param string $payer
     * @return CompletePurchaseResponse
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @param string $isSuccessful
     * @return CompletePurchaseResponse
     */
    public function setIsSuccessful($isSuccessful)
    {
        $this->isSuccessful = $isSuccessful;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsSuccessful()
    {
        return $this->isSuccessful;
    }
}
