<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Filesystem;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Filesystem $filesystem, Filesystem $privateFilesystem): Response
    {
        $filesystem->write('foo.txt', 'content')
            ->delete('foo.txt')
        ;

        $privateFilesystem->write('foo.txt', 'content')
            ->delete('foo.txt')
            ->write('foo.txt', 'content')
            ->delete('foo.txt')
        ;

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
