<?php

namespace App\Command;

use App\Marmalade\Page;
use App\Marmalade\PageRenderer;
use App\Marmalade\Pages;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'marmalade:publish',
    description: 'Publish your marmalade static site.',
)]
class MarmaladePublishCommand extends Command
{
    public function __construct(
        private Pages $pages,
        private PageRenderer $renderer,

        #[Autowire('%kernel.project_dir%/public/assets')]
        private string $assetsDir,

        #[Autowire('%kernel.project_dir%/var/site')]
        private string $outputDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $io->comment('Publishing assets...');

        $application = $this->getApplication() ?? throw new \RuntimeException('Could not get application');
        $application->setAutoExit(false);
        $application->run(new StringInput('asset-map:compile'), $output);

        $fs->remove($this->outputDir);
        $fs->mkdir($this->outputDir);
        $fs->mirror($this->assetsDir, $this->outputDir.'/assets');
        $fs->remove($this->assetsDir);

        $io->comment('Publishing pages...');

        foreach ($io->progressIterate($this->pages) as $page) {
            assert($page instanceof Page);

            $html = $this->renderer->render($page->path);
            $fs->dumpFile("{$this->outputDir}/{$page->path}.html", $html);
        }

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
