<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

class OmnipayRequest extends AbstractRequest
{
    /**
     * @var \Omnipay\Common\Message\AbstractRequest Omnipay Request object
     */
    protected $_worker;

    /**
     * @var OmnipayMerchant
     */
    public $merchant;

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
}
