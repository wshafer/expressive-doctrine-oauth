<?php

declare(strict_types=1);

namespace WShafer\OAuth\Entity;

use Database\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * Access Token
 *
 * @ORM\Table(
 *     name="clients",
 *     indexes={@ORM\Index(name="idx1_clients", columns={"name"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique_client_name", columns={"name"})}
 * )
 * @ORM\Entity(repositoryClass="OAuth\Repository\ClientRepository")
 */
class Client implements ClientEntityInterface
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
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=255, nullable=false)
     */
    protected $secret;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", length=2000, nullable=false)
     */
    protected $redirectUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="grants", type="string", length=255, nullable=false)
     */
    protected $grants;

    /**
     * @var AuthCode[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AuthCode", mappedBy="client")
     */
    protected $authCodes;

    /**
     * @var AccessToken[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AccessToken", mappedBy="client")
     */
    protected $accessTokens;

    /**
     * @var ScopeEntityInterface[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Scope", inversedBy="clients")
     * @ORM\JoinTable(name="client_scopes",
     *      joinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="scope_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $scopes;

    public function __construct()
    {
        $this->authCodes = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(?string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): ?string
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
        $authCode->setClient($this);
        $this->authCodes->add($authCode);
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
    public function setAccessTokens($accessTokens): void
    {
        $this->accessTokens->clear();

        foreach ($accessTokens as $accessToken) {
            $this->addAccessToken($accessToken);
        }
    }

    /**
     * @param AccessToken $accessToken
     */
    public function addAccessToken(AccessToken $accessToken)
    {
        $accessToken->setClient($this);
        $this->accessTokens->add($accessToken);
    }

    /**
     * @return array
     */
    public function getGrants(): array
    {
        return explode(',', $this->grants);
    }

    /**
     * @param array $grants
     */
    public function setGrants(array $grants): void
    {
        $this->grants = implode(',', $grants);
    }

    /**
     * @return ArrayCollection|ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param ArrayCollection|ScopeEntityInterface[] $scopes
     */
    public function setScopes($scopes): void
    {
        $this->scopes->clear();

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        if ($this->scopes->contains($scope)){
            return;
        }

        $this->scopes->add($scope);
    }

    /*
     * mandatory methods for oauth below
     */
    public function getIdentifier()
    {
        $this->getId();
    }

    public function getRedirectUri()
    {
        $this->getRedirectUrl();
    }
}
