<?php

namespace App\Command;

use App\Icon\IconKitManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ux:icons:kit:require',
    description: 'Require an icon kit',
)]
class IconsKitRequireCommand extends Command
{
    public function __construct(private IconKitManager $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the kit', null, $this->manager->availableKits())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $this->manager->require($name);

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
