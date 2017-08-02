<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\bitpay;

/**
 * BitPay Omnipay Fake Request class
 *
 * The main purpose of this class is to prevent real purchase request
 * dispatching to BitPay.
 *
 * Instead, this class must be initialized with data array,
 * that contains `inputs` key. The inputs will be used in
 * [[OmnipayMerchant]] to create an appropriate response.
 *
 */
class FakeRequest extends \hiqdev\php\merchant\OmnipayRequest
{
    public function getWorker()
    {
        return $this;
    }
}
