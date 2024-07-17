<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoteController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('index.html.twig');
        }

        switch ($button = $request->request->get('button')) {
            case 'on':
                dump('on logic');
                break;
            case 'off':
                dump('off logic');
                break;
            case 'volume-up':
                dump('volume-up logic');
                break;
            case 'volume-down':
                dump('volume-down logic');
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown button "%s" pressed', $button));
        }

        $this->addFlash('success', sprintf('Button "%s" pressed', $button));

        return $this->redirectToRoute('homepage');
    }
}
