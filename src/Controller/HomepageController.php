<?php

namespace App\Controller;

use App\Message\MessageA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(MessageBusInterface $bus): Response
    {
        foreach (\range(1, 10) as $i) {
            $bus->dispatch(new MessageA('my message!', (bool) \random_int(0, 100) < 10));
        }

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
