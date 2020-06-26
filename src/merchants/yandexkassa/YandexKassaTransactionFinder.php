<?php

namespace hiqdev\php\merchant\merchants\yandexkassa;

use hiqdev\php\merchant\TransactionFinder\TransactionFinderInterface;
use Psr\Http\Message\ServerRequestInterface;

class YandexKassaTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['object']['metadata']['transactionId'] ?? null;
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
