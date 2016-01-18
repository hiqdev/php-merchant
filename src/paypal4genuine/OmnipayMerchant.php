<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\paypal;

/**
 * PayPal Omnipay Merchant class.
 */
class OmnipayMerchant extends \hiqdev\php\merchant\OmnipayMerchant
{
    /**
     * Returns the normalized gateway $name.
     *
     * @see normalizeGateway
     * @param string $name The gateway name. Defaults to `$this->gateway`
     * @return string
     */
    public function getGateway($name = null)
    {
        $result = parent::getGateway($name);

        return $result === 'PayPal' ? 'PayPal_Express' : $result;
    }

    public function prepareData(array $data)
    {
        return array_merge([
            'username' => $data['purse'],
            'password' => $data['secret'],
        ], $data);
    }
}
