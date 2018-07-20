<?php

declare(strict_types=1);

namespace WShafer\OAuth\Entity;

use Database\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * Access Token
 *
 * @ORM\Table(
 *     name="refresh_tokens",
 *     indexes={
 *         @ORM\Index(name="idx1_refresh_tokens", columns={"access_token_id"}),
 *         @ORM\Index(name="idx2_refresh_tokens", columns={"token"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="OAuth\Repository\RefreshTokenRepository")
 */
class RefreshToken implements RefreshTokenEntityInterface
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
     * @var AccessTokenEntityInterface
     *
     * @ORM\OneToOne(targetEntity="AccessToken", inversedBy="refreshToken")
     * @ORM\JoinColumn(name="access_token_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $accessToken;

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

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /*
     * mandatory methods for oauth below
     */
    public function getIdentifier()
    {
        $this->getToken();
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
}
