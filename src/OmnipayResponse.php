<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

/**
 * Omnipay Response class.
 */
class OmnipayResponse extends AbstractResponse
{
    /**
     * @var \Omnipay\Common\Message\AbstractResponse|\Omnipay\Common\Message\RedirectResponseInterface
     */
    protected $_worker;

    /**
     * @var OmnipayRequest Omnipay Request object
     */
    public $request;

    /**
     * @return \Omnipay\Common\Message\AbstractResponse|\Omnipay\Common\Message\RedirectResponseInterface|\Omnipay\Common\Message\ResponseInterface
     */
    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = $this->request->getWorker()->send();
        }

        return $this->_worker;
    }

    /**
     * {@inheritdoc}
     */
    public function redirect()
    {
        $this->getWorker()->redirect();
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return $this->getWorker()->isRedirect();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getWorker()->isSuccessful();
    }

    public function __call($name, $args)
    {
        if (method_exists($this->getWorker(), $name)) {
            return call_user_func_array([$this->getWorker(), $name], $args);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->getWorker()->getAmount();
    }

    public function getTime()
    {
        return date('c');
    }

    public function getVar($name)
    {
        return $this->getWorker()->getData()[$name];
    }
}
