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

use hiqdev\cashew\DTO\PaymentFlow\Card\CardPaymentRequest;
use hiqdev\php\merchant\InvoiceInterface;

/**
 * Interface PaymentCardMerchatInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface PaymentCardMerchantInterface extends MerchantInterface
{
    /**
     * @param InvoiceInterface $invoice
     * @return CardPaymentRequest
     */
    public function requestTransaction(InvoiceInterface $invoice): CardPaymentRequest;
}
