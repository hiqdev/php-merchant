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

/**
 * Interface GatewayFactoryInterface.
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
