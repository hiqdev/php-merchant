<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\response;

use DateTimeImmutable;

/**
 * Class CardAuthorizationResponse is created when a card was authorized
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CardAuthorizationResponse extends CompletePurchaseResponse
{
    private ?DateTimeImmutable $expirationTime = null;

    public function setExpirationTime(DateTimeImmutable $dateTime): self
    {
        $this->expirationTime = $dateTime;

        return $this;
    }

    public function getExpirationTime(): ?DateTimeImmutable
    {
        return $this->expirationTime;
    }
}
