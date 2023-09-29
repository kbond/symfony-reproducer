<?php

namespace App\Controller;

use App\FormRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
class ContactController extends AbstractController
{
    public function __invoke(FormRequest $request): Response
    {
        $form = $request->validate([
            'name' => new Assert\NotBlank(),
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'department' => [new Assert\NotBlank(), new Assert\Choice(['sales', 'marketing'])],
            'message' => [new Assert\NotBlank(), new Assert\Length(['min' => 10])],
            'agree' => [new Assert\NotNull(['message' => 'You must agree!'])],
            'screenshots' => [new Assert\All(new Assert\Image()), new Assert\Count(max: 3)],
            'newsletter' => null,
        ]);

        if ($form->isSubmittedAndValid()) {
            dd($form->data());
        }

        return new Response(
            $this->renderView('contact.html.twig', ['form' => $form]),
            $form->isValid() ? 200 : 422
        );
    }
}
