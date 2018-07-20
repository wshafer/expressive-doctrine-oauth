<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Scope;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\Scope;
use WShafer\OAuth\Repository\ScopeRepository;

class CreateFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('Oauth\Doctrine\EntityManager');

        /** @var ScopeRepository $scopeRepo */
        $scopeRepo = $entityManager->getRepository(Scope::class);

        /** @var Config $config */
        $config = $container->get(Config::class);

        return new Create($scopeRepo, $config);
    }
}
