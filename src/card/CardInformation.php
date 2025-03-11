<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\card;

use DateTimeImmutable;

final class CardInformation
{
    public string $last4;

    public ?string $brand;

    public ?string $fingerprint;

    public DateTimeImmutable $expirationTime;

    public bool $removeAfterFirstUse = false;
}
