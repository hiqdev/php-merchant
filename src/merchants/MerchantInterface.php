<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;

/**
 * Interface AdapterInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MerchantInterface
{
    /**
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface;

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice);

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data);
}
