<?php

namespace App\Command;

use App\Icon\IconRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'ux:icons:require',
    description: 'Install icons locally from blade-ui-kit.com',
)]
class IconsRequireCommand extends Command
{
    public function __construct(private IconRegistry $registry, private HttpClientInterface $http)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('names', InputArgument::IS_ARRAY, 'Icon name from blade-ui-kit.com')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $names = $input->getArgument('names');

        foreach ($names as $name) {
            $io->comment(sprintf('Installing <info>%s</info>...', $name));

            $this->registry->add($name, $this->parseSvg($name));

            $io->text(sprintf('<info>Installed</info>, render with <comment><twig:Icon name="%s" /></comment>.', $name));
            $io->newLine();
        }

        return Command::SUCCESS;
    }

    private function parseSvg(string $name): string
    {
        $html = $this->http->request('GET', sprintf('https://blade-ui-kit.com/blade-icons/%s', $name))->getContent();
        $svg = (new Crawler($html))->filter('#icon-detail svg')->first();

        if (!$svg->count()) {
            throw new \RuntimeException(sprintf('Could not parse icon "%s".', $name));
        }

        return $svg->outerHtml();
    }
}
