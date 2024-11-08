<?php

namespace Thvvger\RequestValidator\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thvvger\RequestValidator\Services\FileGenerator;

#[AsCommand(name: 'make:request', description: 'Create a request class for validation')]
class GenerateClassCommand extends Command
{
    private FileGenerator $fileGenerator;

    public function __construct(FileGenerator $fileGenerator)
    {
        parent::__construct();
        $this->fileGenerator = $fileGenerator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('className', InputArgument::REQUIRED, 'The name of the class to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $className = $input->getArgument('className');

        $this->fileGenerator->generateClass($className, false);

        $output->writeln("Class $className generated successfully in src/Request.");

        return Command::SUCCESS;
    }

}