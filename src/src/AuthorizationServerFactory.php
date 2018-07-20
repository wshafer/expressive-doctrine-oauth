<?php

declare(strict_types=1);

namespace WShafer\OAuth;

use Doctrine\ORM\EntityManager;
use League\OAuth2\Server\AuthorizationServer;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\AccessToken;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Entity\Scope;
use WShafer\OAuth\Repository\AccessTokenRepository;
use WShafer\OAuth\Repository\ClientRepository;
use WShafer\OAuth\Repository\ScopeRepository;
use Psr\Container\ContainerInterface;

class AuthorizationServerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('Oauth\Doctrine\EntityManager');

        /** @var ClientRepository $clientRepository */
        $clientRepository = $entityManager->getRepository(Client::class);

        /** @var AccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $entityManager->getRepository(AccessToken::class);

        /** @var ScopeRepository $scopeRepository */
        $scopeRepository = $entityManager->getRepository(Scope::class);

        /** @var Config $config */
        $config = $container->get(Config::class);

        $encryptionKey = include $config->getEncryptionKeyPath();

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $config->getPrivateKeyPath(),
            $encryptionKey
        );

        foreach ($config->getGrants() as $grantServiceName) {
            $grant = $container->get($grantServiceName);

            $server->enableGrantType(
                $grant,
                $config->getAccessTokenExpireInterval()
            );
        }

        return $server;
    }
}
