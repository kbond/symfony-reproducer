<?php

namespace App\Controller;

use App\Entity\Sample;
use App\Entity\SampleProxy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $object = new Sample(1, 'value1', 'value2', 'value3');
        $proxy = new SampleProxy($object);

        dd($proxy, $proxy->getId(), $proxy->getProp1(), $proxy->getProp2(), $proxy->prop3);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
