<?php

namespace App\Controller;

use App\Dto\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\FormRequest;

#[Route('/contact2', name: 'contact2', methods: ["GET", "POST"])]
class Contact2Controller extends AbstractController
{
    public function __invoke(FormRequest $request): Response
    {
        $form = $request->validate($dto = new Contact());

        if ($form->isSubmittedAndValid()) {
            $data = $form->data();

            foreach ($data['screenshots'] as $key => $value) {
                $data['screenshots'][$key] = $value->getClientOriginalName();
            }

            $this->addFlash('success', 'Submitted with '.\json_encode($data));

            return $this->redirectToRoute('contact2');
        }

        // TODO 422 if error
        return $this->render('contact.html.twig', [
            'form' => $form,
        ]);
    }
}
