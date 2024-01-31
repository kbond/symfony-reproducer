<?php

namespace App\Command;

use App\Icon\IconRegistry;
use App\Iconify;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ux:icons:import',
    description: 'Import icon(s) from iconify.design',
)]
class IconsImportCommand extends Command
{
    public function __construct(private IconRegistry $registry, private Iconify $iconify)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'Icon name from iconify.design (suffix with "@<name>" to rename locally)',
                null,
                fn() => $this->iconify->fetchSetNames(),
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $names = $input->getArgument('names');

        foreach ($names as $name) {
            if (preg_match('#^(([\w-]+):([\w-]+))(@([\w-]+))?$#', $name, $matches)) {
                $this->importIcon($io, $matches[2], $matches[3], $matches[5] ?? $matches[3]);

                continue;
            }

            if (preg_match('#^([\w-]+)(@([\w-]+))?$#', $name, $matches)) {
                $this->importSet($io, $matches[1], $matches[3] ?? $matches[1]);

                continue;
            }

            $io->error(sprintf('Invalid icon name "%s".', $name));
        }

        return Command::SUCCESS;
    }

    private function importIcon(SymfonyStyle $io, string $prefix, string $name, string $localName): void
    {
        $io->comment(sprintf('Importing <info>%s:%s</info> as <info>%s</info>...', $prefix, $name, $localName));

        $this->registry->add($localName, $this->iconify->svg($prefix, $name));

        $io->text(sprintf('<info>Imported Icon</info>, render with <comment><twig:Icon name="%s" /></comment>.', $localName));
        $io->newLine();
    }

    private function importSet(SymfonyStyle $io, string $name, string $localName): void
    {
        $io->comment(sprintf('Downloading set <info>%s</info>...', $name));

        $this->registry->addSet($localName, $this->iconify->fetchSet($name));

        $io->comment(sprintf('Imported set <info>%s</info> as <info>%s</info>...', $name, $localName));
    }
}
