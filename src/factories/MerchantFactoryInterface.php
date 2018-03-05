<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\merchants\MerchantInterface;

/**
 * Interface MerchantFactoryInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MerchantFactoryInterface
{
    public function build(string $name): MerchantInterface;
}
