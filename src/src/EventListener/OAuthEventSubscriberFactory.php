<?php

namespace WShafer\OAuth\EventListener;

use WShafer\OAuth\Config\Config;
use Psr\Container\ContainerInterface;

class OAuthEventSubscriberFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new OAuthEventSubscriber($container->get(Config::class));
    }
}
