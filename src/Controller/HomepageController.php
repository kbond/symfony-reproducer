<?php

namespace App\Controller;

use App\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): View
    {
        return View::template('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
