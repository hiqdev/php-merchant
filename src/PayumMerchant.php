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
 * PayumMerchant class.
 * To be done... Any help appreciated.
 */
class PayumMerchant extends AbstractMerchant
{
    public $requestClass = 'hiqdev\php\merchant\PayumRequest';

    public $responseClass = 'hiqdev\php\merchant\PayumResponse';
}
