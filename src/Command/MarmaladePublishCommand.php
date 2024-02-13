<?php

namespace App\Command;

use App\Marmalade\Asset;
use App\Marmalade\AssetContextDecorator;
use App\Marmalade\Page;
use App\Marmalade\PageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'marmalade:publish',
    description: 'Publish your marmalade static site.',
)]
class MarmaladePublishCommand extends Command
{
    public function __construct(
        private PageManager $pageManager,
        private RouterInterface $router,
        private AssetContextDecorator $assetContext,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('base-url', mode: InputOption::VALUE_REQUIRED, description: 'The base URL of the site.')
            ->addOption('output-dir', mode: InputOption::VALUE_REQUIRED, description: 'The directory to output the site to.', default: 'var/site')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);
        $baseUrl = $input->getOption('base-url');
        $outputDir = $input->getOption('output-dir');

        if ($this->getApplication()->getKernel()->isDebug()) {
            $io->warning('It looks like you are in debug mode. This command should only be run in production.');
        }

        if ($baseUrl) {
            $this->router->setContext(RequestContext::fromUri($baseUrl));
        }

        if ($basePath = parse_url((string) $baseUrl, PHP_URL_PATH)) {
            $this->assetContext->setBasePath($basePath);
        }

        $fs->remove($outputDir);

        $io->title(sprintf('Publishing site to <info>%s</info>', $outputDir));
        $io->comment('Publishing Pages...');

        foreach ($io->progressIterate($this->pageManager->pages()) as $page) {
            assert($page instanceof Page);

            $html = $this->pageManager->render($page->path);
            $fs->dumpFile("{$outputDir}/{$page->path}.{$page->extension}", $html);
        }

        $io->comment('Publishing Assets...');

        foreach ($io->progressIterate($this->pageManager->assets()) as $asset) {
            assert($asset instanceof Asset);

            $outputPath = "{$outputDir}/{$asset->path}";

            match (true) {
                $asset->data instanceof \SplFileInfo && $asset->data->isDir() => $fs->mirror($asset->data, $outputPath),
                $asset->data instanceof \SplFileInfo => $fs->copy($asset->data, $outputPath),
                default => $fs->dumpFile($outputPath, $asset->data),
            };
        }

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
