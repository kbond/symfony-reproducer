<?php

namespace App\Controller;

use App\Dto\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Zenstruck\FormRequest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class JsonContactController extends AbstractController
{
    #[Route('/contact-json-raw', methods: "POST", defaults: ['_format' => 'json'])]
    public function raw(FormRequest $request): Response
    {
        $form = $request->validateOrFail([
            'name' => new Assert\NotBlank(),
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'department' => [new Assert\NotBlank(), new Assert\Choice(['sales', 'marketing'])],
            'message' => [new Assert\NotBlank(), new Assert\Length(['min' => 10])],
            'agree' => [new Assert\NotNull(['message' => 'You must agree!'])],
            'screenshots' => [new Assert\All(new Assert\Image()), new Assert\Count(max: 3)],
            'newsletter' => null,
        ]);

        return $this->json($form->data(), 201);
    }

    #[Route('/contact-json-dto', methods: "POST", defaults: ['_format' => 'json'])]
    public function dto(FormRequest $request): Response
    {
        return $this->json($request->validateOrFail(new Contact())->object(), 201);
    }
}
