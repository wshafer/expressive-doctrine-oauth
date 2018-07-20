<?php

declare(strict_types=1);

namespace WShafer\OAuth\Grant;

use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Psr\Container\ContainerInterface;

class ClientCredentialsGrantFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ClientCredentialsGrant();
    }
}
