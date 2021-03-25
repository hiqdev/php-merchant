<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

use hiqdev\cashew\Entity\PaymentTransaction\Confirmation;
use hiqdev\cashew\VO\PaymentOption;

interface PaymentRefundInterface
{
    public function refund(Confirmation $confirmation, PaymentOption $option);
}
