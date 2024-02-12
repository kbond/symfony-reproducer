<?php

namespace App\Marmalade;

use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PageRenderer
{
    public function __construct(private Environment $twig, private PageCollection $pages)
    {
    }

    public function render(string $path): string
    {
        $page = $this->pages->get($path);

        return $this->twig->render($page->template, [
            'page' => $page,
            'pages' => $this->pages,
        ]);
    }
}
