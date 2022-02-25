<?php

namespace App\Controller;

use App\ORM\HydrationTracker;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post')]
    public function index(PostRepository $posts, HydrationTracker $tracker): Response
    {
        $posts->findAll();

        $posts->createQueryBuilder('e')->getQuery()->getArrayResult();

        $posts->createQueryBuilder('e')->select('e.id')->getQuery()->getScalarResult();

        dump($tracker->byEntity(), $tracker->byHydrator());

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
