<?php declare(strict_types=1);

namespace hiqdev\php\merchant\response;

class InsufficientFundsResponse extends CompletePurchaseResponse
{
    private ?string $message = null;

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
