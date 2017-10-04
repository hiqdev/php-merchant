<?php

namespace hiqdev\php\merchant\factories;

use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Helper;

class OmnipayFactory implements GatewayFactoryInterface
{
    public function build(string $type, $parameters): GatewayInterface
    {
        $className = Helper::getGatewayClassName($type);
        /** @var GatewayInterface $gateway */
        $gateway = new $className();
        $gateway->initialize($parameters);

        return $gateway;
    }
}
