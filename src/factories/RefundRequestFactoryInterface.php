<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\merchants\RefundRequestInterface;
use Money\Money;

interface RefundRequestFactoryInterface
{
    public function build(string $name, string $refundTransactionId, Money $amount): RefundRequestInterface;
}
