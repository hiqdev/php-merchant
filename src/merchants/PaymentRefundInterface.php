<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

use Omnipay\Stripe\Message\Response;

interface PaymentRefundInterface
{
    public function refund(RefundEntityInterface $refundEntity): Response;
}
