<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface SupportsPreflightNotificationRequestInterface is used for integrations
 * that send a pre-flight notification request and expect some exact response to be
 * sure that service is alive
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface SupportsPreflightNotificationRequestInterface
{
    /**
     * Handles preflight request and responds with a Response, expected by the
     * service provider.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null a response of NULL, when the Request is not a valid pre-flight request
     */
    public function handlePreflightCompletePurchaseRequest(ServerRequestInterface $request): ?ResponseInterface;
}
