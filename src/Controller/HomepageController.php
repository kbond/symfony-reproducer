<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route(path: ['en' => '/', 'fr' => '/fr'], name: 'homepage')]
    public function index(CategoryRepository $repo): Response
    {
        return $this->render('homepage/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }
}
