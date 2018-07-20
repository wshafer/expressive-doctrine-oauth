<?php

declare(strict_types=1);

namespace WShafer\OAuth\Grant;

use Identity\Entity\User;
use Identity\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Grant\PasswordGrant;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\RefreshToken;
use WShafer\OAuth\Repository\RefreshTokenRepository;
use Psr\Container\ContainerInterface;

class PasswordGrantFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('Oauth\Doctrine\EntityManager');

        /** @var UserRepository $userRepo */
        $userRepo = $entityManager->getRepository(User::class);

        /** @var RefreshTokenRepository $refreshTokenRepo */
        $refreshTokenRepo = $entityManager->getRepository(RefreshToken::class);

        /** @var Config $oauthConfig */
        $oauthConfig = $container->get(Config::class);

        $grant = new PasswordGrant(
            $userRepo,
            $refreshTokenRepo
        );

        $grant->setRefreshTokenTTL($oauthConfig->getRefreshTokenExpireInterval());

        return $grant;
    }
}
