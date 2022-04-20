<?php

namespace App\View;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Template extends View
{
    protected function __construct(private string $template, private array $context = [])
    {
        parent::__construct();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        if (!$container->has(Environment::class)) {
            throw new \LogicException(\sprintf('Twig is required to use "%s". Try running "composer require twig".', self::class));
        }

        $response = $response ?? new Response();
        $context = $this->context;

        foreach ($context as $k => $v) {
            if (!$v instanceof FormInterface) {
                continue;
            }

            // auto-create form view
            $context[$k] = $v->createView();

            // set 422 status code if form is submitted and invalid
            if (Response::HTTP_OK === $response->getStatusCode() && $v->isSubmitted() && !$v->isValid()) {
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $response->setContent($container->get(Environment::class)->render($this->template, $context));

        return parent::__invoke($request, $container, $response);
    }
}
