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
 * ResponseInterface declares basic interface all responses have to follow.
 *
 * All responses have to provide:
 * - result info: is successful, is redirect
 * - redirection facility
 */
interface ResponseInterface
{
    /**
     * Checks whether the response requires redirect
     *
     * @return boolean
     */
    public function isRedirect();

    /**
     * Checks whether the response is successful
     *
     * @return boolean
     */
    public function isSuccessful();

    /**
     * Perform the required redirect
     *
     * @void
     */
    public function redirect();
}
