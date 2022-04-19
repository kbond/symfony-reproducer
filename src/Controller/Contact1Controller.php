<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Zenstruck\FormRequest;

#[Route('/contact1', name: 'contact1', methods: ["GET", "POST"])]
class Contact1Controller extends AbstractController
{
    public function __invoke(FormRequest $request): Response
    {
        $form = $request->validate([
            'name' => new Assert\NotBlank(),
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'department' => new Assert\Choice(['sales', 'marketing']),
            'message' => [new Assert\NotBlank(), new Assert\Length(['min' => 10])],
            'agree' => [new Assert\NotNull(['message' => 'You must agree!'])],
            'screenshots' => [new Assert\All(new Assert\Image()), new Assert\Count(max: 3)],
            'newsletter' => null,
        ]);

        if ($form->isSubmittedAndValid()) {
            $data = $form->data();

            foreach ($data['screenshots'] as $key => $value) {
                $data['screenshots'][$key] = $value->getClientOriginalName();
            }

            $this->addFlash('success', 'Submitted with '.\json_encode($data));

            return $this->redirectToRoute('contact1');
        }

        // TODO 422 if error
        return $this->render('contact.html.twig', [
            'form' => $form,
        ]);
    }
}
