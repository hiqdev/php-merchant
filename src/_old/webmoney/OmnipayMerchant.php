<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\webmoney;

/**
 * WebMoney Omnipay Merchant class.
 */
class OmnipayMerchant extends \hiqdev\php\merchant\OmnipayMerchant
{
    public function prepareData(array $data)
    {
        return array_merge([
            'merchantPurse' => $data['purse'],
            'secretKey'     => $data['secret'],
        ], $data);
    }
}
