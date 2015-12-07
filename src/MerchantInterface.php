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
 * MerchantInterface declares basic interface all merchants have to follow.
 *
 * All merchants have to provide:
 * - label: user friendly payment gateway description
 * - request creation
 */
interface MerchantInterface
{
    public function getLabel();

    public function request($type, $data);
}
