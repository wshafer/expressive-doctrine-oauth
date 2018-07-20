<?php
declare(strict_types=1);

namespace WShafer\OAuth\Middleware;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use WShafer\OAuth\Exception\OAuthHttpProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface
{
    /**
     * @var ResourceServer
     */
    protected $server;

    protected $responseFactory;

    public function __construct(
        ResourceServer $server,
        callable $responseFactory
    ) {
        $this->server = $server;
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            throw OAuthHttpProblem::create($exception);
        }

        // Pass the request and response on to the next responder in the chain
        return $handler->handle($request);
    }
}
