<?php

declare(strict_types=1);

namespace WShafer\OAuth\Repository;

use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use WShafer\OAuth\Entity\RefreshToken;
use WShafer\OAuth\Exception\RefreshTokenNotFoundException;

class RefreshTokenRepository extends EntityRepository implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }

    /**
     * @param RefreshTokenEntityInterface|RefreshToken $refreshTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $exists = null;

        try {
            $exists = $this->findOneByToken($refreshTokenEntity->getToken());
        } catch (RefreshTokenNotFoundException $e) {}

        if ($exists) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->_em->persist($refreshTokenEntity);
        $this->_em->flush($refreshTokenEntity);
    }

    public function revokeRefreshToken($tokenId)
    {
        $token = $this->findOneByToken($tokenId);
        $token->setRevoked(true);
        $this->_em->flush($token);
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $token = $this->findOneByToken($tokenId);
        return $token->isRevoked();
    }

    /**
     * @param $token
     * @return RefreshToken
     * @throws RefreshTokenNotFoundException
     */
    public function findOneByToken($token)
    {
        /** @var RefreshToken $refreshToken */
        $refreshToken = $this->findOneBy(['token' => $token]);

        if (!$refreshToken) {
            throw new RefreshTokenNotFoundException(
                'A refresh token by the token id of '.$token.' was not found'
            );
        }

        return $refreshToken;
    }
}