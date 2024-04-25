<?php

namespace App\Controller;

use App\Dto\Thing;
use App\Form\Field;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FormController extends AbstractController
{
    #[Route('/form', name: 'app_form')]
    public function index(Request $request): Response
    {
        $form = $this->createFormFrom(new Thing());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('form/index.html.twig', [
            'controller_name' => 'FormController',
            'form' => $form,
        ]);
    }

    private function createFormFrom(object $dto): FormInterface
    {
        $formBuilder = $this->createFormBuilder($dto);

        foreach (Field::fieldsFrom($dto) as [$field, $property]) {
            $formBuilder->add($field->name, $field->type, $field->options);
        }

        return $formBuilder->getForm();
    }
}
