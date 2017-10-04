<?php

namespace hiqdev\php\merchant;

use Money\Currency;

/**
 * Interface InvoiceInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface InvoiceInterface
{
    /**
     * @return string
     */
    public function getClient();

    /**
     * @param string $client
     * @return InvoiceInterface
     */
    public function setClient($client);

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @param string $sum
     * @return InvoiceInterface
     */
    public function setAmount($sum);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return InvoiceInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getReturnUrl();

    /**
     * @param string $returnUrl
     * @return InvoiceInterface
     */
    public function setReturnUrl($returnUrl);

    /**
     * @return string
     */
    public function getNotifyUrl();

    /**
     * @param string $notifyUrl
     * @return InvoiceInterface
     */
    public function setNotifyUrl($notifyUrl);

    /**
     * @return string
     */
    public function getCancelUrl();

    /**
     * @param string $cancelUrl
     * @return InvoiceInterface
     */
    public function setCancelUrl($cancelUrl);


    /**
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * @param Currency $currency
     * @return InvoiceInterface
     */
    public function setCurrency(Currency $currency);

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return InvoiceInterface
     */
    public function setId($id);
}
