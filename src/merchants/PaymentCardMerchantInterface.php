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

use hiqdev\php\merchant\card\CardInformation;
use hiqdev\php\merchant\exceptions\MerchantException;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;

/**
 * Interface PaymentCardMerchantInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface PaymentCardMerchantInterface extends MerchantInterface
{
    /**
     * @param InvoiceInterface $invoice
     * @return CompletePurchaseResponse|RedirectPurchaseResponse
     * @throws MerchantException when charge fails completely
     */
    public function chargeCard(InvoiceInterface $invoice);

    public function fetchCardInformation(string $clientId, string $token): CardInformation;

    public function removePaymentMethod(string $paymentMethod): void;
//    Probably, new API should be imtroduced:
//
//    /**
//     * @param InvoiceInterface $invoice
//     * @return UncapturedCompletePurchaseResponse|RedirectPurchaseResponse
//     * @throws MerchantException when unable to authorize a card
//     */
//    public function authorizeCard(InvoiceInterface $invoice);
//
//    /**
//     * @param UncapturedCompletePurchaseResponse $purchaseResponse
//     * @return CompletePurchaseResponse
//     */
//    public function captureAuthorizedAmount(UncapturedCompletePurchaseResponse $purchaseResponse, ?Money $amount = null): CompletePurchaseResponse;
//
//    public function cancelCardAuthorization(UncapturedCompletePurchaseResponse $purchaseResponse): void;
}
