<?php

namespace App\Controller;

use App\Marmalade\PageRenderer;
use App\Marmalade\PageCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MarmaladeController extends AbstractController
{
    public function __construct(private PageRenderer $renderer)
    {
    }

    #[Route('/', name: 'marmalade_index')]
    #[Route('/{path}.html', name: 'marmalade_page', requirements: ['path' => '.+'])]
    public function page(string $path = PageCollection::HOMEPAGE): Response
    {
        return new Response($this->renderer->render($path));
    }
}
