<?php

namespace hiqdev\php\merchant\factories;

use Omnipay\Common\GatewayInterface;

/**
 * Interface GatewayFactoryInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface GatewayFactoryInterface
{
    /**
     * @param string $type
     * @param $parameters
     * @return GatewayInterface
     */
    public function build(string $type, $parameters): GatewayInterface;
}
