<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Collection\Doctrine\EntityRepository;
use Zenstruck\Collection\Doctrine\ObjectRepositoryFactory;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(PostRepository $postRepo, #[Autowire(expression: 'service("zenstruck_collection.orm_repository_factory").create("App\\\Entity\\\Comment")')] EntityRepository $commentRepo): Response
    {
        dd($commentRepo);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
