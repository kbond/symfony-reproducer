<?php

namespace App\Command;

use App\Icon\IconRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    protected function configure(): void
    {
        $this
            ->addArgument(
                'set',
                InputArgument::IS_ARRAY,
                'Icon set names to list',
                null,
                fn() => array_filter(array_keys($this->registry->sets()))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sets = $input->getArgument('set') ?: [''];
        $iconSets = $this->registry->sets();

        if (!$iconSets) {
            $io->error('No icons have been registered.');

            return Command::FAILURE;
        }

        foreach ($sets as $set) {
            if (!isset($iconSets[$set])) {
                $io->error(sprintf('The icon set "%s" does not exist.', $set));

                continue;
            }

            $io->section(sprintf('Icons in "%s" Set', $set ?: '(root)'));
            $io->table(
                ['Name'],
                array_map(fn(string $icon) => [$icon], $iconSets[$set] ?? []),
            );
        }

        $io->section('Available Sets');
        $io->table(
            ['Set', '# Icons'],
            array_map(
                fn(string $icon, array $icons) => [$icon ?: '(root)', count($icons)],
                array_keys($iconSets),
                $iconSets,
            )
        );

        return Command::SUCCESS;

        if (!$sets) {
            $io->section('Icons in "root" Set');
            $io->table(
                ['Name'],
                array_map(fn(string $icon) => [$icon], $iconSets[''] ?? []),
            );

            $otherSets = $iconSets;
            unset($otherSets['']);

            if (!$otherSets) {
                return Command::SUCCESS;
            }

            $io->section('Available Sets');
            $io->table(
                ['Set', '# Icons'],
                array_map(
                    fn(string $icon, array $icons) => [$icon, count($icons)],
                    array_keys($otherSets),
                    $otherSets,
                )
            );

            return Command::SUCCESS;
        }




        return Command::SUCCESS;
    }
}
