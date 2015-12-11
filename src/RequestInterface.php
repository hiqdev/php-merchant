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
 * RequestInterface declares basic interface all requests have to follow.
 *
 * All requests have to provide:
 *
 * - payment data: amount, currency
 * - response creation with send()
 */
interface RequestInterface
{
    public function getAmount();

    public function getCurrency();

    public function send();
}
