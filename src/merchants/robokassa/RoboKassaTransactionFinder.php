<?php

namespace hiqdev\php\merchant\merchants\robokassa;

use hiqdev\php\merchant\TransactionFinder\TransactionFinderInterface;
use Psr\Http\Message\ServerRequestInterface;

class RoboKassaTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['Shp_TransactionId'] ?? null;
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return 'OK' . $request->getParsedBody()['InvId']; // https://docs.robokassa.ru/#1250
    }
}
