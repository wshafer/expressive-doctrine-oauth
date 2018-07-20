<?php

declare(strict_types=1);

namespace WShafer\OAuth\Entity;

use Database\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Scope
 *
 * @ORM\Table(
 *     name="scopes",
 *     indexes={@ORM\Index(name="idx1_scopes", columns={"name"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique_scope_name", columns={"name"})}
 * )
 * @ORM\Entity(repositoryClass="OAuth\Repository\ScopeRepository")
 */
class Scope implements ScopeEntityInterface
{
    use TimestampableTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", length=11, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var Client[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Client", mappedBy="scopes")
     */
    protected $clients;

    /**
     * @var UserEntityInterface[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="\Identity\Entity\User", mappedBy="scopes")
     */
    protected $users;

    /**
     * @var AccessToken[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AccessToken", mappedBy="scopes")
     */
    protected $accessTokens;

    /**
     * @var AuthCode[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AuthCode", mappedBy="scopes")
     */
    protected $authCodes;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
        $this->authCodes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection|Client[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @param ArrayCollection|Client[] $clients
     */
    public function setClients(array $clients): void
    {
        $this->clients->clear();

        foreach ($clients as $client) {
            $this->addClient($client);
        }
    }

    public function addClient(Client $client)
    {
        $client->addScope($this);

        if ($this->clients->contains($client)) {
            return;
        }

        $this->clients->add($client);
    }

    /**
     * @return ArrayCollection|UserEntityInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection|UserEntityInterface[] $users
     */
    public function setUsers(array $users): void
    {
        $this->users->clear();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    public function addUser(UserEntityInterface $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
    }

    /**
     * @return ArrayCollection|AccessToken[]
     */
    public function getAccessTokens()
    {
        return $this->accessTokens;
    }

    /**
     * @param ArrayCollection|AccessToken[] $accessTokens
     */
    public function setAccessTokens(array $accessTokens): void
    {
        $this->accessTokens->clear();

        foreach ($accessTokens as $accessToken) {
            $this->addAccessToken($accessToken);
        }
    }

    public function addAccessToken(AccessToken $accessToken)
    {
        $accessToken->addScope($this);

        if ($this->accessTokens->contains($accessToken)) {
            return;
        }

        $this->accessTokens->add($accessToken);
    }

    /**
     * @return ArrayCollection|AuthCode[]
     */
    public function getAuthCodes()
    {
        return $this->authCodes;
    }

    /**
     * @param ArrayCollection|AuthCode[] $authCodes
     */
    public function setAuthCodes($authCodes): void
    {
        $this->authCodes->clear();

        foreach ($authCodes as $authCode) {
            $this->addAuthCode($authCode);
        }
    }

    public function addAuthCode(AuthCode $authCode)
    {
        $authCode->addScope($this);

        if ($this->authCodes->contains($authCode)) {
            return;
        }

        $this->authCodes->add($authCode);
    }

    /*
     * mandatory methods for oauth below
     */
    public function getIdentifier()
    {
        return $this->getName();
    }

    public function jsonSerialize()
    {
        return $this->name;
    }
}
