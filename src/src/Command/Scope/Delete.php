<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command\Scope;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Delete extends AbstractScopeCommand
{
    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('oauth:scope:delete')
            ->setDescription('Delete an existing scope')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Scope name'
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $this->scopeRepository->deleteOneByName($name);
        $output->writeln('Scope: '.$name.' deleted.');
    }
}
