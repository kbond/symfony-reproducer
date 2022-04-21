<?php

namespace App;

use App\View\File;
use App\View\Json;
use App\View\Redirect;
use App\View\Redirect\RouteRedirect;
use App\View\Redirect\UrlRedirect;
use App\View\Serialized;
use App\View\Stream;
use App\View\Template;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class View
{
    /** @var \ArrayObject<int,callable(Response):void> */
    private \ArrayObject $manipulators;

    protected function __construct()
    {
        $this->manipulators = new \ArrayObject();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        $response = $response ?? new Response();

        foreach ($this->manipulators as $manipulator) {
            $manipulator($response);
        }

        return $response;
    }

    final public static function template(string $template, array $context = []): Template
    {
        return new Template($template, $context);
    }

    /**
     * @param array $context Serialization context
     */
    final public static function json(mixed $data, array $context = []): Json
    {
        return new Json($data, $context);
    }

    /**
     * @param array $context Serialization context
     */
    final public static function serialize(mixed $data, array $context = []): Serialized
    {
        return new Serialized($data, $context);
    }

    final public static function redirectTo(string $url): UrlRedirect
    {
        return Redirect::to($url);
    }

    final public static function noContent(): View
    {
        return (new self())->withStatus(Response::HTTP_NO_CONTENT);
    }

    final public static function redirectToRoute(
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): RouteRedirect {
        return Redirect::toRoute($name, $parameters, $referenceType);
    }

    final public static function file(\SplFileInfo|string $file): File
    {
        return new File($file);
    }

    /**
     * @param resource|callable():void $resource
     * @param null|string              $type     MIME type or extension
     */
    final public static function stream($resource, ?string $type = null): Stream
    {
        return new Stream($resource, $type);
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
        $this->manipulators[] = $manipulator;

        return $this;
    }
}
