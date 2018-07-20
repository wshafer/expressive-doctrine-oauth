<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Client;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Entity\Scope;
use WShafer\OAuth\Repository\ClientRepository;
use WShafer\OAuth\Repository\ScopeRepository;

class CreateFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('Oauth\Doctrine\EntityManager');

        /** @var ClientRepository $clientRepo */
        $clientRepo = $entityManager->getRepository(Client::class);

        /** @var ScopeRepository $scopeRepo */
        $scopeRepo = $entityManager->getRepository(Scope::class);

        /** @var Config $config */
        $config = $container->get(Config::class);

        return new Create($clientRepo, $scopeRepo, $config);
    }
}
