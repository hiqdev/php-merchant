<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

interface CanIgnore3dSecureInterface
{
    public function withIgnore3dSecure(): CanIgnore3dSecureInterface;

    public function is3dSecureIgnorred(): bool;
}
