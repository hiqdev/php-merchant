<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\interkassa;

/**
 * Interkassa Omnipay Merchant class.
 */
class OmnipayMerchant extends \hiqdev\php\merchant\OmnipayMerchant
{
    public function prepareData(array $data)
    {
        return array_merge([
            'signKey'       => $data['secret'],
        ], $data);
    }
}
