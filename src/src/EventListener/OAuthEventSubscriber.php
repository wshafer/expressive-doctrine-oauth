<?php

namespace WShafer\OAuth\EventListener;

use Database\Event\FetchRepositoryEvent;
use Doctrine\Common\EventSubscriber;
use WShafer\OAuth\Config\Config;

class OAuthEventSubscriber implements EventSubscriber
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function fetchRepositoryEvent(FetchRepositoryEvent $event)
    {
        $repository = $event->getRepository();

        if ($repository instanceof ConfigAwareInterface) {
            $repository->setConfig($this->config);
        }
    }

    public function getSubscribedEvents()
    {
        return [FetchRepositoryEvent::EVENT_NAME];
    }
}
