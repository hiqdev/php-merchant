<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

use hiqdev\php\merchant\exceptions\RequestMerchantException;

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
    /**
     * Validates and returns the formatted amount.
     *
     * @throws RequestMerchantException on any validation failure.
     * @return string The amount formatted to the correct number of decimal places for the selected currency.
     */
    public function getAmount();

    /**
     * Returns the currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Send the request.
     *
     * @return ResponseInterface
     */
    public function send();
}
