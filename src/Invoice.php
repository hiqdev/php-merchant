<?php

namespace hiqdev\php\merchant;

use Money\Currency;

/**
 * Class Invoice
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class Invoice implements InvoiceInterface
{
    protected $id;
    protected $client;
    protected $amount;
    protected $description;
    protected $returnUrl;
    protected $notifyUrl;
    protected $cancelUrl;
    protected $currency;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Invoice
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;

        return $this;
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     * @return Invoice
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }
}
