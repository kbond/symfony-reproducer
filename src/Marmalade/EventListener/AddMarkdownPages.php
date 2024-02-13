<?php

namespace App\Marmalade\EventListener;

use App\Marmalade\Event\BuildPages;
use App\Marmalade\Page;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Finder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsEventListener]
final class AddMarkdownPages
{
    public function __construct(
        private CommonMarkConverter $markdown,

        #[Autowire('%kernel.project_dir%/templates/marmalade/pages')]
        private string $dir,
        private string $markdownTemplate = 'marmalade/markdown_page.html.twig',
    ) {
    }

    public function __invoke(BuildPages $event): void
    {
        $finder = (new Finder())->in($this->dir)->name('*.md')->files();

        foreach ($finder as $file) {
            $path = substr($file->getRelativePathname(), 0, -3);
            $contents = $file->getContents();
            $metadata[] = ['_markdown' => $contents];
            $rendered = $this->markdown->convert($contents);

            if ($rendered instanceof RenderedContentWithFrontMatter) {
                $metadata[] = $rendered->getFrontMatter();
            }

            $event->addPage($path, new Page(
                $path,
                $event->generateUrl($path, 'html'),
                $this->markdownTemplate,
                'html',
                array_merge(...$metadata),
            ));
        }
    }
}
