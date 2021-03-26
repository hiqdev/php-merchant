<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

interface PaymentRefundInterface
{
    public function refund(RefundRequestInterface $refundEntity): void;
}
