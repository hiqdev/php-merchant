<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants;

/**
 * Interface RemoteCustomerAwareMerchant
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface RemoteCustomerAwareMerchant extends MerchantInterface
{
    public function createCustomer(string $email): string;
}
