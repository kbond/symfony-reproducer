<?php

namespace App\Marmalade;

use App\Marmalade\Event\AddPages;
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

    public function __construct(
        private EventDispatcherInterface $events,
        private Environment $twig,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function render(string $path): string
    {
        $page = $this->pages()->get($path);

        return $this->twig->render($page->template, [
            'page' => $page,
            'pages' => $this->pages,
        ]);
    }

    public function pages(): PageCollection
    {
        if (isset($this->pages)) {
            return $this->pages;
        }

        $this->events->dispatch($event = new AddPages($this->router));

        return $this->pages = new PageCollection($event->pages());
    }
}
