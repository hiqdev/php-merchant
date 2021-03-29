<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

interface CanIgnore3dSecureInterface
{
    public function withIgnore3dSecure(): void;

    public function is3dSecureIgnorred(): bool;
}
