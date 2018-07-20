<?php

namespace WShafer\OAuth\EventListener;

use WShafer\OAuth\Config\Config;

interface ConfigAwareInterface
{
    public function getConfig() : Config;
    public function setConfig(Config $config);
}
