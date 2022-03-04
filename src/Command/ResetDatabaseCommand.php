<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

#[AsCommand(
    name: 'app:reset-database',
    description: 'Reset database'
)]
class ResetDatabaseCommand extends Command
{
    private SymfonyStyle $io;

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('Reset database in progress');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->runSymfonyCommand('doctrine:database:drop', ["--force" => true]);
            $this->runSymfonyCommand('doctrine:database:create', []);
            $this->runSymfonyCommand('doctrine:migrations:migrate', ["--no-interaction" => true]);
            $this->runSymfonyCommand('hautelook:fixtures:load', ["--no-interaction" => true]);
            $this->io->success('Recreate database with success');
            return Command::SUCCESS;
        } catch (Exception $exception) {
            $this->io->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @param string $command
     * @param array $options
     * @return void
     * @throws Exception
     */
    private function runSymfonyCommand(string $command, array $options)
    {
        $application = $this->getApplication();
        $application->setCatchExceptions(false);
        $application->setAutoExit(false);
        $options["command"] = $command;
        $application->run(new ArrayInput($options));
    }
}