<?php

namespace App\Controller;

use App\Message\MessageA;
use App\Messenger\Monitor\Stamp\TagStamp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new MessageA('my message!'), [new TagStamp('tag2')]);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
