<?php declare(strict_types=1);

namespace hiqdev\php\merchant\exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    protected string $context;

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
