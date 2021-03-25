<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants\stripe;

use hiqdev\php\merchant\merchants\RefundEntityInterface;
use Money\Money;

final class StripeRefundEntity implements RefundEntityInterface
{
    private string $remoteId;
    private Money $amount;

    public function __construct(string $remoteId, Money $amount)
    {
        $this->remoteId = $remoteId;
        $this->amount = $amount;
    }

    public function getRefundTransactionId(): string
    {
        return $this->remoteId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }
}
