<?php declare(strict_types=1);

namespace hiqdev\php\merchant\exceptions;

class InsufficientFundsException extends MerchantException
{
    protected array $contextData = [];

    public function withContextData(array $contextData): self
    {
        $this->contextData = $contextData;

        return $this;
    }

    public function getContextData(): array
    {
        return $this->contextData;
    }
}
