<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post')]
    public function index(PostRepository $posts, ManagerRegistry $r): Response
    {
        $posts->findAll();

        $posts->createQueryBuilder('e')->getQuery()->execute(null, Query::HYDRATE_ARRAY);

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
