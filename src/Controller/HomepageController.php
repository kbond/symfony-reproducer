<?php

namespace App\Controller;

use App\Message\MessageA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Messenger\Monitor\Stamp\Tag;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(MessageBusInterface $bus, MailerInterface $mailer): Response
    {
//        $bus->dispatch(new MessageA('from controller 1'));
//        $bus->dispatch(new MessageA('from controller 2', throw: true));
//        $bus->dispatch(new MessageA('from controller 3'));
//        $bus->dispatch(new MessageA('from controller 4'), [new Tag('forth')]);

        $email = (new Email())
            ->subject('some subject')
            ->from('admin@example.com')
            ->to('kevin@example.com')
            ->text('some body')
        ;
        $email->getHeaders()->add(new TagHeader('example'));

        $mailer->send($email);

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
