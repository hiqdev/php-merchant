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
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = $this->request->getWorker()->send();
        }

        return $this->_worker;
    }

    public function redirect()
    {
        return $this->getWorker()->redirect();
    }

    public function isRedirect()
    {
        return $this->getWorker()->isRedirect();
    }

    public function isSuccessful()
    {
        return $this->getWorker()->isSuccessful();
    }
}
