<?php

namespace App\Marmalade\Event;

use App\Marmalade\Page;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AddPages extends Event
{
    /** @var array<string,Page> */
    private array $pages = [];

    /**
     * @internal
     */
    public function __construct(private UrlGeneratorInterface $router)
    {
    }

    public function addPage(string $path, Page $page): void
    {
        if (isset($this->pages[$path])) {
            throw new \InvalidArgumentException(sprintf('Duplicate page "%s" found.', $path));
        }

        $this->pages[$path] = $page;
    }

    public function generateUrl(string $path, string $extension): string
    {
        if ('index' === $path) {
            return $this->router->generate('marmalade_index', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->router->generate(
            'marmalade_page',
            ['path' => $path, '_format' => $extension],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @return array<string,Page>
     */
    public function pages(): array
    {
        return $this->pages;
    }
}
