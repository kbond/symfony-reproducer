<?php

namespace App\Marmalade\EventListener;

use App\Marmalade\Event\AddPages;
use App\Marmalade\Page;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsEventListener]
final class AddTwigPages
{
    public function __construct(
        private Environment $twig,

        #[Autowire('%kernel.project_dir%/templates')]
        private string $dir,
        private string $prefix = 'marmalade/pages',
    ) {
    }

    public function __invoke(AddPages $event): void
    {
        $finder = (new Finder())->in("{$this->dir}/{$this->prefix}")->name('*.twig')->files();

        foreach ($finder as $file) {
            [$path, $extension] = self::normalizePath($file);

            $template = $this->twig->load("{$this->prefix}/{$file->getRelativePathname()}");
            $metadata = $template->hasBlock('metadata') ? Yaml::parse($template->renderBlock('metadata')) : [];

            $event->addPage($path, new Page(
                $path,
                $event->generateUrl($path, $extension),
                $template->getSourceContext()->getName(),
                $extension,
                $metadata,
            ));

        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function normalizePath(SplFileInfo $file): array
    {
        $path = substr($file->getRelativePathname(), 0, -5);

        if (!$extension = pathinfo($path, PATHINFO_EXTENSION)) {
            return [$path, 'html'];
        }

        return [
            substr($path, 0, -(strlen($extension) + 1)),
            $extension,
        ];
    }
}
