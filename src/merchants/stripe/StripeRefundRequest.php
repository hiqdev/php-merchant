<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants\stripe;

use hiqdev\php\merchant\merchants\RefundRequestInterface;
use Money\Money;

final class StripeRefundRequest implements RefundRequestInterface
{
    private string $refundTransactionId;
    private Money $amount;

    public function __construct(string $refundTransactionId, Money $amount)
    {
        $this->remoteId = $refundTransactionId;
        $this->amount = $amount;
    }

    public function getRefundTransactionId(): string
    {
        return $this->refundTransactionId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
