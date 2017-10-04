<?php

namespace hiqdev\php\merchant\factories;

use Omnipay\Common\GatewayInterface;

interface GatewayFactoryInterface
{
    /**
     * @param string $type
     * @param $parameters
     * @return GatewayInterface
     */
    public function build(string $type, $parameters): GatewayInterface;
}
