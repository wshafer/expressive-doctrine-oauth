<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command;

use Identity\Entity\User;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Entity\Client;
use WShafer\OAuth\Repository\ScopeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class CommandAbstract extends Command
{
    /** @var ScopeRepository */
    protected $scopeRepository;

    /** @var Config */
    protected $config;

    public function __construct(ScopeRepository $scopeRepository, Config $config)
    {
        $this->scopeRepository = $scopeRepository;
        $this->config = $config;
        parent::__construct();
    }

    protected function getSecret(InputInterface $input, OutputInterface $output, $type): string
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new Question('<question>Enter new '.$type.':</question> ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $secret = false;
        $verify = false;

        while (!$secret) {
            $secret = $helper->ask($input, $output, $question);
        }

        $verifyQuestion = new Question('<question>Retype new '.$type.':</question> ');
        $verifyQuestion->setHidden(true);
        $verifyQuestion->setHiddenFallback(false);

        while (!$verify) {
            $verify = $helper->ask($input, $output, $verifyQuestion);
        }

        if ($secret !== $verify) {
            $output->writeln('<error>Secrets do not match.  Please try again.</error>');
            $secret = $this->getSecret($input, $output, $type);
        }

        return $this->hashString($secret);
    }

    protected function hashString($string)
    {
        return password_hash(
            $string,
            $this->config->getPasswordHashAlgorithm(),
            $this->config->getPasswordHashOptions()
        );
    }

    protected function getScopes(InputInterface $input, OutputInterface $output, $current = null)
    {
        if ($current
            && !$current instanceof Client
            && !$current instanceof User
        ) {
            throw new \RuntimeException(
                "Invalid current entity passed in"
            );
        }

        $alreadySelected = [];
        $allScopes = $this->scopeRepository->getAllScopeNames();

        if ($current) {
            $currentScopes = $current->getScopes();

            foreach ($currentScopes as $scope) {
                $alreadySelected[] = $scope->getIdentifier();
            }
        }

        return $this->getChoices($input, $output, 'scopes', $allScopes, $alreadySelected);
    }

    protected function getChoices(
        InputInterface $input,
        OutputInterface $output,
        $type,
        $allChoices,
        $choicesSelected = []
    ) {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        sort($allChoices);

        while (true) {
            sort($choicesSelected);
            $displayGrants = array_combine(range(1, count($allChoices)), $allChoices);
            $displayGrants[] = 'Next';

            $questionText = '<info>Selected '.$type.': '.implode(', ',$choicesSelected)."</info>\n"
                .'<question>Select allowed '.$type.' (select next to continue):</question> ';

            $grantsQuestion = new ChoiceQuestion(
                $questionText,
                $displayGrants,
                end($displayGrants)
            );

            $choice = $helper->ask($input, $output, $grantsQuestion);

            if ($choice === 'Next' && !empty($choicesSelected)) {
                break;
            } elseif ($choice === 'Next' && empty($choicesSelected)) {
                $output->writeln('<error>At least one '.$type.' must be provided</error>');
                continue;
            }

            if (in_array($choice, $choicesSelected)) {
                $choicesSelected = array_diff($choicesSelected, array($choice));
            } else {
                $choicesSelected[] = $choice;
            }
        }

        return $choicesSelected;
    }
}