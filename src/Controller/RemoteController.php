<?php

namespace App\Controller;

use App\Remote\ButtonRemote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoteController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET', 'POST'])]
    public function index(Request $request, ButtonRemote $remote): Response
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('index.html.twig', [
                'remote' => $remote,
            ]);
        }

        $remote->press($button = $request->request->getString('button'));

        $this->addFlash('success', sprintf('Button "%s" pressed', $button));

        return $this->redirectToRoute('homepage');
    }
}
