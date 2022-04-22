<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\FormRequest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Route('/api/posts', defaults: ['_format' => 'json'])]
final class JsonPostController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(PostRepository $posts): Response
    {
        return $this->json($posts->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(Post $post): Response
    {
        return $this->json($post);
    }

    #[Route('', methods: ['POST'])]
    public function post(FormRequest $request, PostRepository $posts): Response
    {
        $form = $request->validateOrFail(Post::class);

        $posts->add($post = $form->object());

        return $this->json($post, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function put(FormRequest $request, Post $post, PostRepository $posts): Response
    {
        $request->validateOrFail($post);
        $posts->add($post);

        return $this->get($post);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Post $post, PostRepository $posts): Response
    {
        $posts->remove($post);

        return new Response(null, 204);
    }
}
