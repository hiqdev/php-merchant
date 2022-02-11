<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\response;

use DateTimeInterface;
use Money\Currency;
use Money\Money;

/**
 * Interface CompletePurchaseResponseInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface CompletePurchaseResponseInterface
{
    public function getTransactionId(): string;

    public function getCurrency(): Currency;

    public function getAmount(): Money;

    public function getFee(): Money;

    public function getTime(): DateTimeInterface;

    public function getTransactionReference(): string;

    public function getPayer(): string;

    public function getIsSuccessful(): bool;

    public function setFee(Money $fee): self;

    public function setPaymentMethod(?string $method): self;

    public function getPaymentMethod(): ?string;
}
