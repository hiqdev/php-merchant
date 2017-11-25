<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\phpunit;

use PHPUnit_Framework_MockObject_Invocation;
use PHPUnit_Framework_MockObject_Stub_ReturnCallback;

class PHPUnit_Framework_MockObject_Stub_ReturnCallbackWithInvocationScope extends PHPUnit_Framework_MockObject_Stub_ReturnCallback
{
    public function invoke(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        return call_user_func_array($this->callback->bindTo($invocation->object), $invocation->parameters);
    }
}
