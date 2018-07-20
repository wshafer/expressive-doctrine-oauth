<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command;

use Interop\Container\ContainerInterface;
use WShafer\OAuth\Config\Config;

class KeyGeneratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(Config::class);
        return new KeyGenerator($config);
    }
}
