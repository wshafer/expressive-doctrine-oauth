<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Client;

use WShafer\OAuth\Exception\ClientExistsException;
use WShafer\OAuth\Exception\ClientNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends AbstractClientCommand
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('oauth:client:create')
            ->setDescription('Create a new client')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Client name'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->getClientName($input);
        $secret = $this->getSecret($input, $output, 'client secret');
        $redirectUrl = $this->getRedirectUrl($input, $output);
        $allowedGrants = $this->getGrants($input, $output);
        $allowedScopes = $this->getScopes($input, $output);

        $this->clientRepository->createNewClient(
            $name,
            $secret,
            $redirectUrl,
            $allowedGrants,
            $allowedScopes
        );

        $output->writeln('Client '.$name.' created successfully');
    }

    protected function getClientName(InputInterface $input)
    {
        $name = $input->getArgument('name');

        $exists = null;

        try {
            $exists = $this->clientRepository->findOneByName($name);
        } catch (ClientNotFoundException $e) {}

        if ($exists) {
            throw new ClientExistsException(
                'A client by the name of "'.$name.'" already exists'
            );
        }

        return $name;
    }
}
