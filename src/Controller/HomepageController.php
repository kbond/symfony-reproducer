<?php

namespace App\Controller;

use Algolia\AlgoliaSearch\SearchClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Algolia\Search\Index;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(SearchClient $client, Index $postIndex): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
