<?php

namespace App\Controller;

use App\Twig\AttributeBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extension\EscaperExtension;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default')]
    public function index(): Response
    {
        $this->container->get('twig')
            ->getExtension(EscaperExtension::class)
            ->addSafeClass(AttributeBag::class, ['html'])
        ;

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
