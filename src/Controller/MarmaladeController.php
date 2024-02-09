<?php

namespace App\Controller;

use App\Marmalade\PageRenderer;
use App\Marmalade\Pages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MarmaladeController extends AbstractController
{
    public function __construct(private PageRenderer $renderer)
    {
    }


    public function index(): Response
    {
        return $this->page(Pages::HOMEPAGE);
    }

    #[Route('/', name: 'marmalade_index')]
    #[Route('/{path}.html', name: 'marmalade_page', requirements: ['path' => '.+'])]
    public function page(string $path = Pages::HOMEPAGE): Response
    {
        return new Response($this->renderer->render($path));
    }
}
