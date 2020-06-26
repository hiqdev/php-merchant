<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\TransactionFinder;

use Psr\Http\Message\ServerRequestInterface;

interface TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string;

    public function getSuccessResponseText(ServerRequestInterface $request): ?string;
}
