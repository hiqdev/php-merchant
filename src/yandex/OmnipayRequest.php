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
    /**
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = $this->merchant->getWorker()->{$this->getType()}($this->getData());
        }

        return $this->_worker;
    }

    public function getData()
    {
        return array_merge(parent::getData(), [
            'account' => $this->data['purse'],
            'form_comment' => $this->data['description'],
            'orderId' => $this->data['transactionId'],
        ]);
    }
}
