<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Scope;

use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Repository\ScopeRepository;
use Symfony\Component\Console\Command\Command;

class AbstractScopeCommand extends Command
{
    /** @var ScopeRepository */
    protected $scopeRepository;

    protected $config;

    public function __construct(
        ScopeRepository $scopeRepository,
        Config $config
    ) {
        $this->scopeRepository = $scopeRepository;
        $this->config = $config;
        parent::__construct();
    }
}
