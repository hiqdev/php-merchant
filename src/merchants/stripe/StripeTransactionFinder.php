<?php

declare(strict_types=1);

namespace hiqdev\php\merchant\merchants\stripe;

use hiqdev\php\merchant\TransactionFinder\TransactionFinderInterface;
use Psr\Http\Message\ServerRequestInterface;

class StripeTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['data']['object']['metadata']['transactionId'] ?? null;
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
