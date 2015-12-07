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
abstract class AbstractResponse extends \yii\base\Object implements ResponseInterface
{
    public $merchant;

    public $request;

    abstract public function redirect();

    abstract public function isRedirect();

    abstract public function isSuccessful();
}
