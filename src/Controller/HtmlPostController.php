<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\FormRequest;

#[Route('/html-post')]
class HtmlPostController extends AbstractController
{
    #[Route('', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(FormRequest $request, PostRepository $postRepository): Response
    {
        $form = $request->validate(Post::class);

        if ($form->isSubmittedAndValid()) {
            $postRepository->add($form->object()); // $form->object() is autocompletable!

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return new Response(
            $this->renderView('post/new.html.twig', ['form' => $form]),
            $form->isValid() ? 200 : 422
        );
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'PUT'])]
    public function edit(FormRequest $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $request->validate($post);

        if ($form->isSubmittedAndValid()) {
            $postRepository->add($post);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return new Response(
            $this->renderView('post/edit.html.twig', ['form' => $form, 'post' => $post]),
            $form->isValid() ? 200 : 422
        );
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['DELETE'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->id(), $request->request->get('_token'))) {
            $postRepository->remove($post);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
