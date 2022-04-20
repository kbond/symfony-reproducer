<?php

namespace App\View;

use App\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Template extends View
{
    protected function __construct(private string $template, private array $context = [])
    {
    }

    /**
     * @internal
     */
    public function __invoke(Environment $twig): Response
    {
        $response = new Response();
        $context = $this->context;

        foreach ($context as $k => $v) {
            if (!$v instanceof FormInterface) {
                continue;
            }

            // auto-create form view
            $context[$k] = $v->createView();

            // set 422 status code if form is submitted and invalid
            if (200 === $response->getStatusCode() && $v->isSubmitted() && !$v->isValid()) {
                $response->setStatusCode(422);
            }
        }

        return $this->manipulate(new Response($twig->render($this->template, $context)));
    }
}
