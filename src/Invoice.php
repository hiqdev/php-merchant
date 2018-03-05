<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

use Money\Currency;
use Money\Money;

/**
 * Class Invoice.
 *
 * Many methods depend on [[amount]], so make sure to set as early as possible.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class Invoice implements InvoiceInterface
{
    private $id;
    private $client;
    private $amount;
    private $description;
    private $returnUrl;
    private $notifyUrl;
    private $cancelUrl;
    private $returnMethod;
    private $notifyMethod;
    private $cancelMethod;
    private $currency;

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
        $this->id = (string) $id;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = (string) $client;

        return $this;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function setAmount(Money $amount)
    {
        $this->amount = $amount;
        $this->currency = $amount->getCurrency();

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = (string) $description;

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
     * @return string
     */
    public function getReturnMethod()
    {
        return $this->returnMethod;
    }

    /**
     * @param string $returnMethod
     * @return InvoiceInterface
     */
    public function setReturnMethod($returnMethod)
    {
        $this->returnMethod = $returnMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotifyMethod()
    {
        return $this->notifyMethod;
    }

    /**
     * @param string $notifyMethod
     * @return InvoiceInterface
     */
    public function setNotifyMethod($notifyMethod)
    {
        $this->notifyMethod = $notifyMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelMethod()
    {
        return $this->cancelMethod;
    }

    /**
     * @param string $cancelMethod
     * @return InvoiceInterface
     */
    public function setCancelMethod($cancelMethod)
    {
        $this->cancelMethod = $cancelMethod;

        return $this;
    }
}
