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
 * Interface InvoiceInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface InvoiceInterface
{
    /**
     * @return Client
     */
    public function getClient();

    /**
     * @param string|Client $client
     * @return InvoiceInterface
     */
    public function setClient($client);

    /**
     * @return Money
     */
    public function getAmount(): Money;

    /**
     * @param Money $sum
     * @return InvoiceInterface
     */
    public function setAmount(Money $sum);

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
    public function getReturnMethod();

    /**
     * @param string $returnMethod
     * @return InvoiceInterface
     */
    public function setReturnMethod($returnMethod);

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
    public function getNotifyMethod();

    /**
     * @param string $notifyMethod
     * @return InvoiceInterface
     */
    public function setNotifyMethod($notifyMethod);

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
     * @return string
     */
    public function getCancelMethod();

    /**
     * @param string $cancelMethod
     * @return InvoiceInterface
     */
    public function setCancelMethod($cancelMethod);

    /**
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return InvoiceInterface
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getPreferredPaymentMethod(): ?string;

    /**
     * @param string $paymentMethod
     * @return InvoiceInterface
     */
    public function setPreferredPaymentMethod(string $paymentMethod);
}
