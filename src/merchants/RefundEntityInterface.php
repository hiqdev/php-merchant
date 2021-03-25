<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

use Money\Money;

interface RefundEntityInterface
{
    public function getRefundTransactionId(): string;

    public function getAmount(): Money;
}
