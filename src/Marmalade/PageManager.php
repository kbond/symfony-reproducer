<?php

namespace App\Marmalade;

use App\Marmalade\Event\BuildAssets;
use App\Marmalade\Event\BuildPages;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class PageManager
{
    private PageCollection $pages;

    /** @var array<string,Asset> */
    private array $assets;

    public function __construct(
        private EventDispatcherInterface $events,
        private Environment $twig,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function render(string $path, ?string $format = null): string
    {
        if ($this->pages()->has($path)) {
            $page = $this->pages()->get($path);

            return $this->twig->render($page->template, [
                'page' => $page,
                'pages' => $this->pages,
            ]);
        }

        $assetPath = $format ? "{$path}.{$format}" : $path;

        if (isset($this->assets()[$assetPath])) {
            return $this->assets()[$assetPath]->content();
        }

        throw new \InvalidArgumentException(sprintf('Page "%s" not found.', $path));
    }

    public function pages(): PageCollection
    {
        if (isset($this->pages)) {
            return $this->pages;
        }

        $this->events->dispatch($event = new BuildPages($this->router));

        return $this->pages = new PageCollection($event->pages());
    }

    /**
     * @return array<string,Asset>
     */
    public function assets(): array
    {
        if (isset($this->assets)) {
            return $this->assets;
        }

        $this->events->dispatch($event = new BuildAssets());

        return $this->assets = $event->assets();
    }
}
