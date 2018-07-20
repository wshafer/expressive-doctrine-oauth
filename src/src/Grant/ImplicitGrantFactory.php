<?php

declare(strict_types=1);

namespace WShafer\OAuth\Grant;

use League\OAuth2\Server\Grant\ImplicitGrant;
use WShafer\OAuth\Config\Config;
use Psr\Container\ContainerInterface;

class ImplicitGrantFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var Config $oauthConfig */
        $oauthConfig = $container->get(Config::class);

        return new ImplicitGrant($oauthConfig->getAccessTokenExpireInterval());
    }
}
