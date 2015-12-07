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
    public function isRedirect();

    public function isSuccessful();

    public function redirect();
}
