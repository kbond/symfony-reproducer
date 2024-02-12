<?php

namespace App\Command;

use App\Marmalade\AssetContextDecorator;
use App\Marmalade\Page;
use App\Marmalade\PageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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

        #[Autowire('%kernel.project_dir%/public/assets')]
        private string $assetsDir,

        #[Autowire('%kernel.project_dir%/var/site')]
        private string $outputDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('base-url', mode: InputOption::VALUE_REQUIRED, description: 'The base URL of the site.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);
        $baseUrl = $input->getOption('base-url');

        if ($baseUrl) {
            $this->router->setContext(RequestContext::fromUri($baseUrl));
        }

        if ($basePath = parse_url((string) $baseUrl, PHP_URL_PATH)) {
            $this->assetContext->setBasePath($basePath);
        }

        $io->comment('Publishing assets...');

        $application = $this->getApplication() ?? throw new \RuntimeException('Could not get application');
        $application->setAutoExit(false);
        $application->run(new StringInput('asset-map:compile'), $output);

        $fs->remove($this->outputDir);
        $fs->mkdir($this->outputDir);
        $fs->mirror($this->assetsDir, $this->outputDir.'/assets');
        $fs->remove($this->assetsDir);

        $io->comment('Publishing pages...');

        foreach ($io->progressIterate($this->pageManager->pages()) as $page) {
            assert($page instanceof Page);

            $html = $this->pageManager->render($page->path);
            $fs->dumpFile("{$this->outputDir}/{$page->path}.{$page->extension}", $html);
        }

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
