<?php

declare(strict_types=1);

namespace WShafer\OAuth;

use Doctrine\ORM\EntityManager;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\ResourceServer;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\AccessToken;
use WShafer\OAuth\Repository\AccessTokenRepository;
use Psr\Container\ContainerInterface;

class ResourceServerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('Oauth\Doctrine\EntityManager');

        /** @var AccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $entityManager->getRepository(AccessToken::class);

        /** @var Config $config */
        $config = $container->get(Config::class);

        $validator = $container->get(BearerTokenValidator::class);

        return new ResourceServer($accessTokenRepository, $config->getPublicKeyPath(), $validator);
    }
}
