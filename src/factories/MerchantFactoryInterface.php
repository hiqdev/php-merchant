<?php

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\merchants\MerchantInterface;

interface MerchantFactoryInterface
{
    public function build(string $name): MerchantInterface;
}
