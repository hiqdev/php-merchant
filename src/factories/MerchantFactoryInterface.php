<?php

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\merchants\MerchantInterface;

/**
 * Interface MerchantFactoryInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MerchantFactoryInterface
{
    public function build(string $name): MerchantInterface;
}
