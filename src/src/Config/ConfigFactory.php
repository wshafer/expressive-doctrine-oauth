<?php

declare(strict_types=1);

namespace WShafer\OAuth\Config;

use Psr\Container\ContainerInterface;

class ConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        $oauthConfig = $config['oauth2'] ?? [];

        return new Config($oauthConfig);
    }
}