<?php

declare(strict_types=1);

namespace WShafer\OAuth\Entity;

use Identity\Entity\User;
use Database\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Auth Code
 *
 * @ORM\Table(
 *     name="auth_codes",
 *     indexes={
 *         @ORM\Index(name="idx1_auth_codes", columns={"token"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="OAuth\Repository\AuthCodeRepository")
 */
class AuthCode implements AuthCodeEntityInterface
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
     * @var string|null
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=false)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", length=2000, nullable=false)
     */
    protected $redirectUrl;

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
     * @ORM\ManyToOne(targetEntity="\Identity\Entity\User", inversedBy="authCode")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $user;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="authCodes")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $client;

    /**
     * @var Scope[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Scope", inversedBy="authCodes")
     * @ORM\JoinTable(name="auth_code_scopes",
     *      joinColumns={@ORM\JoinColumn(name="auth_code_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="scope_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $scopes;

    //PlaceHolder
    /** @var integer */
    protected $userIdentifier;

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
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param null|string $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
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

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return ArrayCollection|Scope[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param Scope[] $scopes
     */
    public function setScopes(array $scopes)
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

    /*
     * mandatory methods for oauth below
     */
    public function getRedirectUri()
    {
        return $this->getRedirectUrl();
    }

    public function setRedirectUri($uri)
    {
        $this->setRedirectUrl($uri);
    }

    public function getIdentifier()
    {
        return $this->getToken();
    }

    public function setIdentifier($identifier)
    {
        $this->setToken($identifier);
    }

    public function getExpiryDateTime()
    {
        return $this->getExpires();
    }

    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->setExpires($dateTime);
    }

    public function setUserIdentifier($identifier)
    {
        $this->userIdentifier = $identifier;
    }

    public function getUserIdentifier()
    {
        if (!empty($this->userIdentifier)) {
            return $this->userIdentifier;
        }

        return $this->user->getIdentifier();
    }
}
