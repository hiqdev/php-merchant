<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponseInterface;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;

/**
 * Interface HostedPaymentPageMerchantInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface HostedPaymentPageMerchantInterface extends MerchantInterface
{
    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice);

    /**
     * @param array $data
     * @return CompletePurchaseResponseInterface
     */
    public function completePurchase($data);
}
