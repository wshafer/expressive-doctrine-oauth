<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Client;

use Doctrine\ORM\EntityManager;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Exception\ClientExistsException;
use WShafer\OAuth\Exception\ClientNotFoundException;
use WShafer\OAuth\Exception\InvalidRedirectUrl;
use WShafer\OAuth\Repository\ClientRepository;
use WShafer\OAuth\Repository\ScopeRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Modify extends AbstractClientCommand
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
            ->setName('oauth:client:modify')
            ->setDescription('Modify an existing clients name or redirect url')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Client Id'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'New Client Name'
            )
            ->addOption(
                'redirect',
                null,
                InputOption::VALUE_OPTIONAL,
                'New Client Redirect Url'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getClient($input);

        $newName = $input->getOption('name');
        $newRedirect = $input->getOption('redirect');

        if (!$newName && !$newRedirect) {
            throw new \InvalidArgumentException(
                'Missing new name or redirect to update'
            );
        }

        if ($newName && $this->isNameValid($newName)) {
            $client->setName($newName);
            $output->writeln('<info>Name updated to: '.$newName.'</info>');
        }

        if ($newRedirect) {
            $filteredRedirect = filter_var($newRedirect, FILTER_VALIDATE_URL);

            if (!$filteredRedirect) {
                $this->entityManager->flush($client);
                throw new InvalidRedirectUrl($newRedirect.' is invalid.');
            }

            $client->setRedirectUrl($filteredRedirect);
            $output->writeln('<info>Redirect URL updated to: '.$filteredRedirect.'</info>');
        }

        $this->entityManager->flush($client);
        $output->writeln('Complete');
    }

    protected function isNameValid(string $newName)
    {
        try {
            $this->clientRepository->findOneByName($newName);
        } catch (ClientNotFoundException $e) {
            return true;
        }

        throw new ClientExistsException(
            'Client by the name of "'.$newName.'" already exists'
        );
    }
}
