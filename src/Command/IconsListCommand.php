<?php

namespace App\Command;

use App\Icon\IconRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ux:icons:list',
    description: 'List available icons',
)]
class IconsListCommand extends Command
{
    public function __construct(private IconRegistry $registry)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->newLine();
        $io->table(
            ['Available Icons'],
            array_map(fn($n) => [$n], iterator_to_array($this->registry->names())),
        );

        return Command::SUCCESS;
    }
}
