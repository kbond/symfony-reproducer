<?php

namespace App\Controller;

use App\Dto\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Zenstruck\FormRequest;

class ContactController extends AbstractController
{
    #[Route('/contact-html-raw', name: 'contact_html_raw', methods: ["GET", "POST"])]
    public function htmlRaw(FormRequest $request): Response
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
            dd($form->data());
        }

        // TODO 422 if error
        return $this->render('contact.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/contact-html-dto', name: 'contact_html_dto', methods: ["GET", "POST"])]
    public function htmlDto(FormRequest $request): Response
    {
        $form = $request->validate(new Contact());

        if ($form->isSubmittedAndValid()) {
            dd($form->data());
        }

        // TODO 422 if error
        return $this->render('contact.html.twig', [
            'form' => $form,
        ]);
    }
}
