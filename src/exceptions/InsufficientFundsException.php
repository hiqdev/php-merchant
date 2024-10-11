<?php declare(strict_types=1);

namespace hiqdev\php\merchant\exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    protected array $contextData = [];

    public function setContextData(array $contextData): self
    {
        $this->contextData = $contextData;

        return $this;
    }

    public function getContextData(): array
    {
        return $this->contextData;
    }
}
