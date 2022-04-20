<?php

namespace App;

use App\View\Redirect;
use App\View\Redirect\RouteRedirect;
use App\View\Redirect\UrlRedirect;
use App\View\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class View
{
    /** @var array<int,callable(Response):void> */
    private array $responseManipulators = [];

    final public static function template(string $template, array $context = []): Template
    {
        return new Template($template, $context);
    }

    final public static function redirectTo(string $url): UrlRedirect
    {
        return Redirect::to($url);
    }

    final public static function redirectToRoute(
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): RouteRedirect {
        return Redirect::toRoute($name, $parameters, $referenceType);
    }

    final public function withStatus(int $code): static
    {
        return $this->withResponse(fn(Response $r) => $r->setStatusCode($code));
    }

    final public function withHeader(string $header, string|array|null $values, bool $replace = true): static
    {
        return $this->withResponse(fn(Response $r) => $r->headers->set($header, $values, $replace));
    }

    /**
     * @param callable(Response):void $manipulator
     */
    final public function withResponse(callable $manipulator): static
    {
        $this->responseManipulators[] = $manipulator;

        return $this;
    }

    /**
     * @template T of Response
     *
     * @param T $response
     *
     * @return T
     */
    final protected function manipulate(Response $response): Response
    {
        foreach ($this->responseManipulators as $manipulator) {
            $manipulator($response);
        }

        return $response;
    }
}
