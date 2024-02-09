<?php

namespace App\Marmalade;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PagesFactory
{
    public function __construct(
        private Environment $twig,
        private CommonMarkConverter $markdown,
        private UrlGeneratorInterface $router,

        #[Autowire('%kernel.project_dir%/templates')]
        private string $dir,
        private string $prefix = 'marmalade/pages',
    ) {
    }

    public function __invoke(): Pages
    {
        $finder = (new Finder())->in("{$this->dir}/{$this->prefix}")->name('*.md')->name('*.html.twig')->files();
        $pages = [];

        foreach ($finder as $file) {
            $path = self::normalizePath($file);
            $path = 'index' === $path ? Pages::HOMEPAGE : $path;
            $pages[$path] = new Page(
                $path,
                Pages::HOMEPAGE === $path ? $this->url('marmalade_index') : $this->url('marmalade_page', ['path' => $path]),
                $this->templateFor($file),
                $this->metadataFor($file),
            );
        }

        return new Pages($pages);
    }

    private function url(string $route, array $parameters = []): string
    {
        return $this->router->generate($route, $parameters);
    }

    private function templateFor(SplFileInfo $file): string
    {
        if (str_ends_with($file->getRelativePathname(), '.html.twig')) {
            return $this->twig->load("{$this->prefix}/{$file->getRelativePathname()}")->getSourceContext()->getName();
        }

        return 'marmalade/markdown_page.html.twig';
    }

    private function metadataFor(SplFileInfo $file): array
    {
        if (str_ends_with($file->getRelativePathname(), '.html.twig')) {
            return $this->twigMetadataFor($file);
        }

        $markdown = file_get_contents($file);
        $result = $this->markdown->convert($markdown);

        assert($result instanceof RenderedContentWithFrontMatter);

        return array_merge($result->getFrontMatter(), ['_markdown' => $markdown]);
    }

    private function twigMetadataFor(SplFileInfo $file): array
    {
        $template = $this->twig->load($this->templateFor($file));

        if ($template->hasBlock('metadata')) {
            return Yaml::parse($template->renderBlock('metadata'));
        }

        return [];
    }

    private static function normalizePath(SplFileInfo $file): string
    {
        if (str_ends_with($file->getRelativePathname(), '.html.twig')) {
            return substr($file->getRelativePathname(), 0, -10);
        }

        return substr($file->getRelativePathname(), 0, -3);
    }
}
