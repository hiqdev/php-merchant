<?php

namespace hiqdev\php\merchant\merchants\freekassa;

use hiqdev\php\merchant\TransactionFinder\TransactionFinderInterface;
use Psr\Http\Message\ServerRequestInterface;

class FreeKassaTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['MERCHANT_ORDER_ID'] ?? null;
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return 'YES';
    }
}
