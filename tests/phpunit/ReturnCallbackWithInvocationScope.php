<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\phpunit;

use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\Stub;

class ReturnCallbackWithInvocationScope implements Stub
{
    /**
     * @var \Closure|array
     */
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function invoke(Invocation $invocation)
    {
        if ($invocation instanceof Invocation\ObjectInvocation && $this->callback instanceof \Closure) {
            return \call_user_func_array($this->callback->bindTo($invocation->getObject()), $invocation->getParameters());
        }

        return \call_user_func_array($this->callback, $invocation->getParameters());
    }

    public function toString(): string
    {
        if (\is_array($this->callback)) {
            if (\is_object($this->callback[0])) {
                $class = \get_class($this->callback[0]);
                $type  = '->';
            } else {
                $class = $this->callback[0];
                $type  = '::';
            }

            return \sprintf(
                'return result of user defined callback %s%s%s() with the ' .
                'passed arguments',
                $class,
                $type,
                $this->callback[1]
            );
        }

        return 'return result of user defined callback ' . $this->callback .
            ' with the passed arguments';
    }
}
