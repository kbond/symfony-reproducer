<?php

namespace App\Command;

use App\Icon\IconRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'ux:icons:import',
    description: 'Import icon(s) from iconify.design',
)]
class IconsImportCommand extends Command
{
    public function __construct(private IconRegistry $registry, private HttpClientInterface $http)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('names', InputArgument::IS_ARRAY, 'Icon name from iconify.design (suffix with "@<name>" to rename locally)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $names = $input->getArgument('names');

        foreach ($names as $name) {
            if (!preg_match('#^(([\w-]+):([\w-]+))(@([\w-]+))?$#', $name, $matches)) {
                $io->error(sprintf('Invalid icon name "%s".', $name));

                continue;
            }

            [,,$prefix, $name] = $matches;
            $localName = $matches[5] ?? $matches[3];

            $io->comment(sprintf('Installing <info>%s:%s</info> as <info>%s</info>...', $prefix, $name, $localName));

            $this->registry->add($localName, $this->parseSvg($prefix, $name));

            $io->text(sprintf('<info>Installed</info>, render with <comment><twig:Icon name="%s" /></comment>.', $localName));
            $io->newLine();
        }

        return Command::SUCCESS;
    }

    private function parseSvg(string $prefix, string $name): string
    {
        return $this->http
            ->request('GET', sprintf('https://api.iconify.design/%s/%s.svg', $prefix, $name))
            ->getContent()
        ;
    }
}
