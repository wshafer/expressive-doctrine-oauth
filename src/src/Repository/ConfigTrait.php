<?php

declare(strict_types=1);

namespace WShafer\OAuth\Repository;

use WShafer\OAuth\Config\Config;
use Psr\Container\ContainerInterface;

trait ConfigTrait
{
    /** @var Config */
    protected $config;

    /**
     * @return Config
     */
    public function getConfig() : Config
    {
        return $this->config;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }
}
