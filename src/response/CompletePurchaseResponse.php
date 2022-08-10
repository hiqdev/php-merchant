<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\response;

use Money\Currency;
use Money\Money;

/**
 * Class CompletePurchaseResponse.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CompletePurchaseResponse implements CompletePurchaseResponseInterface
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
     * @var string
     */
    protected $paymentMethod;

    public function getTransactionId(): string
    {
        return $this->transactionId ?? $this->transactionReference;
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

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

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
        $this->currency = $amount->getCurrency();

        return $this;
    }

    public function getFee(): Money
    {
        if ($this->fee !== null) {
            return $this->fee;
        }

        return new Money(0, $this->getCurrency());
    }

    public function setFee($fee): self
    {
        $this->fee = $fee;

        return $this;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time In UTC
     * @return CompletePurchaseResponse
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;

        return $this;
    }

    public function getTransactionReference(): string
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

    public function getPayer(): string
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
     * @param bool $isSuccessful
     * @return CompletePurchaseResponse
     */
    public function setIsSuccessful(bool $isSuccessful): self
    {
        $this->isSuccessful = $isSuccessful;

        return $this;
    }

    public function getIsSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function setPaymentMethod(?string $value): self
    {
        $this->paymentMethod = $value;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }
}
