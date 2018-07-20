<?php

declare(strict_types=1);

namespace WShafer\OAuth\Entity;

use Identity\Entity\User;
use Database\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Access Token
 *
 * @ORM\Table(
 *     name="access_tokens",
 *     indexes={
 *         @ORM\Index(name="idx1_access_tokens", columns={"token"}),
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="OAuth\Repository\AccessTokenRepository")
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
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
     * @var string|null
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     */
    protected $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="date", nullable=false)
     */
    protected $expires;

    /**
     * @var Boolean
     *
     * @ORM\Column(name="revoked", type="boolean", nullable=false, options={"default" : 0})
     */
    protected $revoked = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Identity\Entity\User", inversedBy="accessTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $user;

    /**
     * @var ClientEntityInterface
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="accessTokens")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $client;

    /**
     * @var ScopeEntityInterface[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Scope", inversedBy="accessTokens")
     * @ORM\JoinTable(name="access_token_scopes",
     *      joinColumns={@ORM\JoinColumn(name="access_token_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="scope_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $scopes;

    /**
     * @var RefreshToken
     *
     * @ORM\OneToOne(targetEntity="RefreshToken", mappedBy="accessToken")
     */
    protected $refreshToken;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
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
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return \DateTime
     */
    public function getExpires(): \DateTime
    {
        return $this->expires;
    }

    /**
     * @param \DateTime $expires
     */
    public function setExpires(\DateTime $expires): void
    {
        $this->expires = $expires;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @param bool $revoked
     */
    public function setRevoked(bool $revoked): void
    {
        $this->revoked = $revoked;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     */
    public function setUser(UserEntityInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return ScopeEntityInterface[]|ArrayCollection
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function setScope(array $scopes): void
    {
        $this->scopes->clear();

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        if ($this->scopes->contains($scope)) {
            return;
        }

        $this->scopes->add($scope);
    }

    /**
     * @return RefreshToken
     */
    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }

    /**
     * @param RefreshToken $refreshToken
     */
    public function setRefreshToken(RefreshToken $refreshToken): void
    {
        $refreshToken->setAccessToken($this);
        $this->refreshToken = $refreshToken;
    }

    /*
     * mandatory methods for oauth below
     */
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getToken();
    }

    /**
     * @param mixed $identifier
     * @return void
     */
    public function setIdentifier($identifier): void
    {
        $this->setToken($identifier);
    }

    /**
     * @return \DateTime
     */
    public function getExpiryDateTime()
    {
        return $this->getExpires();
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->setExpires($dateTime);
    }

    /**
     * Not implemented here
     *
     * @param int|null|string $identifier
     */
    public function setUserIdentifier($identifier): void
    {
        return;
    }

    public function getUserIdentifier()
    {
        $this->getUser()->getIdentifier();
    }

    /**
     * @return ClientEntityInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }
}
