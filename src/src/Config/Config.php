<?php

declare(strict_types=1);

namespace WShafer\OAuth\Config;

use WShafer\OAuth\Exception\InvalidConfigException;

class Config
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getPrivateKeyPath(): string
    {
        if (empty($this->config['privateKeyPath'])) {
            throw new InvalidConfigException('Unable to locate privateKeyPath in config');
        }

        return $this->config['privateKeyPath'];
    }

    public function getPublicKeyPath(): string
    {
        if (empty($this->config['publicKeyPath'])) {
            throw new InvalidConfigException('Unable to locate publicKeyPath in config');
        }

        return $this->config['publicKeyPath'];
    }

    public function getEncryptionKeyPath(): string
    {
        if (empty($this->config['encryptionKeyPath'])) {
            throw new InvalidConfigException('Unable to locate encryptionKeyPath in config');
        }

        return $this->config['encryptionKeyPath'];
    }

    public function getAccessTokenExpireInterval(): \DateInterval
    {
        if (empty($this->config['accessTokenExpireInterval'])) {
            throw new InvalidConfigException('Unable to locate accessTokenExpireInterval in config');
        }

        return new \DateInterval($this->config['accessTokenExpireInterval']);
    }

    public function getRefreshTokenExpireInterval(): \DateInterval
    {
        if (empty($this->config['refreshTokenExpireInterval'])) {
            throw new InvalidConfigException('Unable to locate refreshTokenExpireInterval in config');
        }

        return new \DateInterval($this->config['refreshTokenExpireInterval']);
    }

    public function getAuthCodeExpireInterval(): \DateInterval
    {
        if (empty($this->config['authCodeExpireInterval'])) {
            throw new InvalidConfigException('Unable to locate authCodeExpireInterval in config');
        }

        return new \DateInterval($this->config['authCodeExpireInterval']);
    }

    public function getPasswordHashAlgorithm() : int
    {
        if (empty($this->config['passwordHash']['algorithm'])) {
            throw new InvalidConfigException('Unable to locate password_hash algorithm in config');
        }

        return $this->config['passwordHash']['algorithm'];
    }

    public function getPasswordHashOptions() : array
    {
        $options = [];

        if (!empty($this->config['passwordHash']['options'])
            && is_array($this->config['passwordHash']['options'])
        ) {
            $options = $this->config['passwordHash']['options'];
        }

        return $options;
    }

    public function getGrants() : array
    {
        if (empty($this->config['grants'])) {
            throw new InvalidConfigException('Unable to locate grants in config');
        }

        return $this->config['grants'];
    }

    public function getAuthenticationRouteName()
    {
        if (empty($this->config['authenticationRouteName'])) {
            throw new InvalidConfigException('Unable to locate authenticationRouteName in config');
        }

        return $this->config['authenticationRouteName'];
    }
}