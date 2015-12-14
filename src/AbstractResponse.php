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
 * Abstract Response class.
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var ResponseInterface
     */
    public $merchant;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * {@inheritdoc}
     */
    abstract public function redirect();

    /**
     * {@inheritdoc}
     */
    abstract public function isRedirect();

    /**
     * {@inheritdoc}
     */
    abstract public function isSuccessful();
}
