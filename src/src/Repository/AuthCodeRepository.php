<?php

declare(strict_types=1);

namespace WShafer\OAuth\Repository;

use Identity\Entity\User;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use WShafer\OAuth\Entity\AuthCode;
use WShafer\OAuth\Exception\AuthCodeNotFoundException;

class AuthCodeRepository extends EntityRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode()
    {
        return new AuthCode();
    }

    /**
     * @param AuthCode|AuthCodeEntityInterface $authCodeEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $exists = null;

        try {
            $exists = $this->findOneByToken($authCodeEntity->getToken());
        } catch (AuthCodeNotFoundException $e) {}


        if ($exists) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        /** @var User $user */
        $user = $this->_em->getRepository(User::class)
            ->find($authCodeEntity->getUserIdentifier());

        $authCodeEntity->setUser($user);

        $this->_em->persist($authCodeEntity);
        $this->_em->flush($authCodeEntity);
    }

    public function revokeAuthCode($codeId)
    {
        $authCode = $this->findOneByToken($codeId);
        $authCode->setRevoked(true);
        $this->_em->flush($authCode);
    }

    public function isAuthCodeRevoked($codeId)
    {
        $authCode = $this->findOneByToken($codeId);
        return $authCode->isRevoked();
    }

    /**
     * @param $token
     * @return AuthCode
     * @throws AuthCodeNotFoundException
     */
    public function findOneByToken($token)
    {
        /** @var AuthCode $authCode */
        $authCode = $this->findOneBy(['token' => $token]);

        if (!$authCode) {
            throw new AuthCodeNotFoundException(
                'A Auth Code by the token id of '.$token.' was not found'
            );
        }

        return $authCode;
    }
}
