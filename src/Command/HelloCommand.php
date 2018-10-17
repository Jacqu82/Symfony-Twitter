<?php

namespace App\Command;

use App\Service\Greeting;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HelloCommand extends Command
{
    /**
     * @var Greeting
     */
    private $greeting;

    public function __construct(Greeting $greeting)
    {
        $this->greeting = $greeting;

        parent::__construct();
    }

//    protected static $defaultName = 'Hello';

    protected function configure()
    {
        $this
            ->setName('app:say-hello')
            ->setDescription('Add a short description for your command')
            ->addArgument('name', InputArgument::REQUIRED)
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        if ($name) {
            $io->note(sprintf('You passed an argument: %s', $name));
        }

//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success($this->greeting->greet($name));
    }

    // bin/console app:say-hello
}
