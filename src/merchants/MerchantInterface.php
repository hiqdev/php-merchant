<?php

namespace hiqdev\php\merchant\merchants;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;


/**
 * Interface AdapterInterface
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
     * @return RedirectPurchaseResponse
     */
    public function completePurchase($data);
}
