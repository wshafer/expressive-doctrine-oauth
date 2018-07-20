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

class Delete extends AbstractClientCommand
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
            ->setName('oauth:client:delete')
            ->setDescription('Delete existing client')
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
        $this->entityManager->remove($client);
        $this->entityManager->flush();
        $output->writeln('Complete');
    }
}
