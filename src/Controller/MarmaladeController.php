<?php

namespace App\Controller;

use App\Marmalade\PageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @internal
 */
class MarmaladeController extends AbstractController
{
    #[Route('/', name: 'marmalade_index')]
    #[Route('/{path}.html', name: 'marmalade_page', requirements: ['path' => '.+'])]
    public function page(PageManager $manager, string $path = 'index'): Response
    {
        return new Response($manager->render($path));
    }
}
