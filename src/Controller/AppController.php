<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class AppController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('homepage.html.twig');
    }

    #[Route('/form1', name: 'form1')]
    public function form1(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'constraints' => new NotBlank(),
                'help' => 'Your full name.'
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Prefer not to say' => null,
                    'Male' => 'male',
                    'Female' => 'female',
                ],
                'expanded' => $request->query->has('expanded'),
            ])
            ->add('profileImage', FileType::class, [
                'required' => false,
                'constraints' => new Image(),
            ])
            ->add('terms', CheckboxType::class, [
                'constraints' => new IsTrue(),
                'help' => 'Do you agree to our terms?'
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('form1.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/form2', name: 'form2')]
    public function form2(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            dd([...$request->getPayload(), ...$request->files->all()]);
        }

        return $this->render('form2.html.twig');
    }
}
