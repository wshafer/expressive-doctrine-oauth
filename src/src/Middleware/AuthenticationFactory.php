<?php

declare(strict_types=1);

namespace WShafer\OAuth\Middleware;

use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ResourceServer $server */
        $server = $container->get(ResourceServer::class);

        /** @var callable $responseFactory */
        $responseFactory = $container->get(ResponseInterface::class);

        return new Authentication($server, $responseFactory);
    }
}
