<?php

namespace hiqdev\php\merchant\response;

class CompletePurchaseResponse
{
    protected $isSuccessful;
    protected $currency;
    protected $amount;
    protected $fee;
    protected $time;
    protected $transactionReference;
    protected $transactionId;

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $transactionId
     * @return CompletePurchaseResponse
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }
    protected $payer;
    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return CompletePurchaseResponse
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return CompletePurchaseResponse
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @param mixed $fee
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
     * @return mixed
     */
    public function getTransactionReference()
    {
        return $this->transactionReference;
    }

    /**
     * @param mixed $transactionReference
     * @return CompletePurchaseResponse
     */
    public function setTransactionReference($transactionReference)
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param mixed $payer
     * @return CompletePurchaseResponse
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @param mixed $isSuccessful
     * @return CompletePurchaseResponse
     */
    public function setIsSuccessful($isSuccessful)
    {
        $this->isSuccessful = $isSuccessful;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsSuccessful()
    {
        return $this->isSuccessful;
    }
}
