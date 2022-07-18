<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants\epayservice;

use hiqdev\php\merchant\TransactionFinder\TransactionFinderInterface;
use Psr\Http\Message\ServerRequestInterface;

class EPayServiceTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['MERCHANT_ORDER_ID'] ?? null;
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
