<?php

namespace App\Controller;

use App\Message\MessageA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Messenger\Monitor\Stamp\Tag;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new MessageA('from controller 1'));
        $bus->dispatch(new MessageA('from controller 2', throw: true));
        $bus->dispatch(new MessageA('from controller 3'));
        $bus->dispatch(new MessageA('from controller 4'), [new Tag('forth')]);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
