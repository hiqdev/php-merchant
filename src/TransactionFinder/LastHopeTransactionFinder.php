<?php

namespace hiqdev\php\merchant\TransactionFinder;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LastHopeTransactionFinder is merchant-agnostic finder that tries to
 * find transaction ID wherever in can.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class LastHopeTransactionFinder implements TransactionFinderInterface
{
    public function findTransactionId(ServerRequestInterface $request): ?string
    {
        return $request->getParsedBody()['transactionId']
            ?? $request->getAttribute('transactionId');
    }

    public function getSuccessResponseText(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
