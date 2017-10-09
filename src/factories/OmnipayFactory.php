<?php

namespace hiqdev\php\merchant\factories;

use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Helper;

/**
 * Class OmnipayFactory
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
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
