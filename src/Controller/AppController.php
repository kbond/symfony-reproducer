<?php

namespace App\Controller;

use App\FormRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AppController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('homepage.html.twig');
    }

    #[Route('/form1', name: 'form1')]
    public function form1(FormRequest $request): Response
    {
        $form = $request->validate([
            'name' => NotBlank::class,
            'email' => [NotBlank::class, Email::class],
            'bio' => null,
            'terms' => null,
        ]);

        if ($form->isSubmittedAndValid()) {
            dd($form->data());
        }

        return new Response(
            $this->renderView('form1.html.twig', ['form' => $form]),
            $form->isValid() ? 200 : 422
        );
    }
}
