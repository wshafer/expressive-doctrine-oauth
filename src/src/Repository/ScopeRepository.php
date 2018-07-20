<?php

declare(strict_types=1);

namespace WShafer\OAuth\Repository;

use Identity\Entity\User;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Entity\Scope;
use WShafer\OAuth\Exception\ScopeExistsException;
use WShafer\OAuth\Exception\ScopeNotFoundException;

class ScopeRepository extends EntityRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->findOneBy(['name' => $identifier]);
    }

    /**
     * @param array $scopes
     * @param string $grantType
     * @param ClientEntityInterface|Client $clientEntity
     * @param null $userIdentifier
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface[]
     * @throws OAuthServerException
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        /** @var User $user */
        $user = $this->_em->getRepository(User::class)
            ->find($userIdentifier);

        /** @var Scope $scope */
        foreach ($scopes as $scope) {
            if (!$clientEntity->getScopes()->contains($scope)
                || !$user->getScopes()->contains($scope)
            ) {
                throw OAuthServerException::invalidScope($scope->getIdentifier());
            }
        }

        return $scopes;
    }

    public function getAllScopeNames()
    {
        $scopes = $this->createQueryBuilder('a')
            ->select('a.name')
            ->getQuery()
            ->getScalarResult();

        if (empty($scopes)) {
            return [];
        }

        return array_column($scopes, 'name');
    }

    public function createScope($name)
    {
        $exists = null;

        try {
            $exists = $this->findOneByName($name);
        } catch (ScopeNotFoundException $e) {}

        if ($exists) {
            throw new ScopeExistsException('A scope by the name of "'.$name.'"" already exists');
        }

        $scope = new Scope();
        $scope->setName($name);

        $this->_em->persist($scope);
        $this->_em->flush($scope);

        return $scope;
    }

    public function deleteOneByName($name)
    {
        $scope = $this->findOneByName($name);
        $this->_em->remove($scope);
        $this->_em->flush();
    }

    public function findOneByName($name)
    {
        $scope = $this->findOneBy(['name' => $name]);

        if (!$scope) {
            throw new ScopeNotFoundException(
                'A scope by the name of '.$scope.' was not found'
            );
        }

        return $scope;
    }
}