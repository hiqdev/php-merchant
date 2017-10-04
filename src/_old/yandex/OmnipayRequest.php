<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\yandex;

/**
 * Yandex Omnipay Merchant class.
 */
class OmnipayRequest extends \hiqdev\php\merchant\OmnipayRequest
{
    public function getData()
    {
        return array_merge(parent::getData(), [
            'method' => 'PC', // https://money.yandex.ru/doc.xml?id=526991
        ]);
    }
}
