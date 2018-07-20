<?php

declare(strict_types=1);

namespace WShafer\OAuth\Repository;

use Database\Repository\ContainerAwareInterface;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Entity\Scope;
use WShafer\OAuth\EventListener\ConfigAwareInterface;
use WShafer\OAuth\Exception\ClientExistsException;
use WShafer\OAuth\Exception\ClientNotFoundException;

class ClientRepository extends EntityRepository implements ClientRepositoryInterface, ConfigAwareInterface
{
    use ConfigTrait;

    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ) {
        try {
            $client = $this->findOneByName($clientIdentifier);
        } catch (ClientNotFoundException $e) {
            return false;
        }

        $allowedGrants = $client->getGrants();

        if (!in_array($grantType, $allowedGrants)) {
            throw OAuthServerException::unsupportedGrantType();
        }

        if (!$mustValidateSecret) {
            return true;
        }

        if (!password_verify($clientSecret, $client->getSecret())) {
            return false;
        }

        $config = $this->getConfig();

        $passwordNeedsRehashed = password_needs_rehash(
            $client->getSecret(),
            $config->getPasswordHashAlgorithm(),
            $config->getPasswordHashOptions()
        );

        if (!$passwordNeedsRehashed) {
            return $client;
        }

        $client->setSecret(password_hash(
            $clientSecret,
            $config->getPasswordHashAlgorithm(),
            $config->getPasswordHashOptions()
        ));

        $this->_em->flush($client);

        return $client;
    }

    public function createNewClient(
        string $name,
        string $secret,
        string $redirectUrl,
        array $allowedGrants,
        array $allowedScopes
    ) {
        $exists = null;

        /** @var ScopeRepository $scopeRepo */
        $scopeRepo = $this->_em->getRepository(Scope::class);

        try {
            $exists = $this->findOneByName($name);
        } catch (ClientNotFoundException $e) {}

        if ($exists) {
            throw new ClientExistsException('A client by the name of "'.$name.'" already exists');
        }

        $client = new Client();
        $client->setName($name);
        $client->setSecret($secret);
        $client->setRedirectUrl($redirectUrl);
        $client->setGrants($allowedGrants);

        foreach ($allowedScopes as $index => $allowedScope) {
            if (!$allowedScope instanceof Scope) {
                $allowedScopes[$index] = $scopeRepo->findOneByName($allowedScope);
            }
        }

        $client->setScopes($allowedScopes);
        $this->_em->persist($client);
        $this->_em->flush($client);
    }

    public function deleteOneByName($name)
    {
        $client = $this->findOneByName($name);
        $this->_em->remove($client);
        $this->_em->flush();
    }

    public function findOneByName($name): Client
    {
        /** @var Client $client */
        $client = $this->findOneBy(['name' => $name]);

        if (!$client) {
            throw new ClientNotFoundException(
                'A client by the name of '.$name.' was not found'
            );
        }

        return $client;
    }
}
