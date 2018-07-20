<?php

declare(strict_types=1);

namespace WShafer\OAuth\Middleware;

use League\OAuth2\Server\AuthorizationServer;
use WShafer\OAuth\Config\Config;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Router\RouterInterface;

class OAuth2Factory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var Config $config */
        $config = $container->get(Config::class);

        /** @var AuthorizationServer $server */
        $server = $container->get(AuthorizationServer::class);

        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);

        /** @var callable $responseFactory */
        $responseFactory = $container->get(ResponseInterface::class);

        return new OAuth2($config, $server, $router, $responseFactory);
    }
}
