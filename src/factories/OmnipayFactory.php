<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\factories;

use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Helper;

/**
 * Class OmnipayFactory.
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
