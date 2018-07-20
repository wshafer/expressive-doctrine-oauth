<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Client;

use Doctrine\ORM\EntityManager;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Repository\ClientRepository;
use WShafer\OAuth\Repository\ScopeRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Secret extends AbstractClientCommand
{
    protected $entityManager;

    public function __construct(
        EntityManager $entityManager,
        ClientRepository $clientRepository,
        ScopeRepository $scopeRepository,
        Config $config
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($clientRepository, $scopeRepository, $config);
    }

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('oauth:client:secret')
            ->setDescription('Change client secret')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Client Id'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getClient($input);
        $secret = $this->getSecret($input, $output, 'client secret');
        $client->setSecret($secret);
        $this->entityManager->flush($client);
        $output->writeln('Complete');
    }
}
