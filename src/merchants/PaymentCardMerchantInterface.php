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
use hiqdev\php\merchant\response\CardAuthorizationResponse;
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

    /**
     * @param InvoiceInterface $invoice
     * @return CardAuthorizationResponse|RedirectPurchaseResponse
     * @throws MerchantException when unable to authorize a card
     */
    public function authorizeCard(InvoiceInterface $invoice);

//    /**
//     * @param RefundRequestInterface $refundRequest
//     * @return CompletePurchaseResponse
//     */
//    public function captureAuthorized(CardAuthorizationResponse $purchaseResponse, ?Money $amount = null): CompletePurchaseResponse;

    /**
     * @param RefundRequestInterface $refundRequest
     */
    public function cancelAuthorization(RefundRequestInterface $refundRequest): void;
}
