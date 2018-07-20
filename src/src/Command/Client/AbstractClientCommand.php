<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Client;

use WShafer\OAuth\Command\CommandAbstract;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Repository\ClientRepository;
use WShafer\OAuth\Repository\ScopeRepository;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AbstractClientCommand extends CommandAbstract
{
    /** @var ClientRepository */
    protected $clientRepository;

    public function __construct(
        ClientRepository $clientRepository,
        ScopeRepository $scopeRepository,
        Config $config
    ) {
        $this->clientRepository = $clientRepository;
        parent::__construct($scopeRepository, $config);
    }

    /**
     * @param InputInterface $input
     * @return \OAuth\Entity\Client
     */
    protected function getClient(InputInterface $input)
    {
        $name = $input->getArgument('name');
        return $this->clientRepository->findOneByName($name);
    }

    protected function getRedirectUrl(InputInterface $input, OutputInterface $output): string
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new Question('<question>Enter the clients redirect url:</question> ');

        $url = '';

        while (!$url) {
            $url = $helper->ask($input, $output, $question);
            $url = filter_var($url, FILTER_VALIDATE_URL);
        }

        return $url;
    }

    protected function getGrants(InputInterface $input, OutputInterface $output, ?Client $currentClient = null)
    {
        $alreadySelected = [];
        $allGrants = array_keys($this->config->getGrants());
        if ($currentClient) {
            $alreadySelected = $currentClient->getGrants();
        }

        return $this->getChoices($input, $output, 'grants', $allGrants, $alreadySelected);
    }
}