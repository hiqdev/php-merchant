<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

interface CanIgnore3dSecureMerchantInterface
{
    public function withIgnore3dSecure(): CanIgnore3dSecureMerchantInterface;

    public function is3dSecureIgnored(): bool;
}
