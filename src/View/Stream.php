<?php

namespace App\View;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\MimeTypesInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Stream extends View
{
    /** @var resource|callable():void */
    private $resource;

    /**
     * @param resource|callable():void $resource
     * @param null|string              $type     MIME type or extension
     */
    protected function __construct($resource, private ?string $type = null)
    {
        parent::__construct();

        if (!\is_callable($resource) && !\is_resource($resource)) {
            throw new \InvalidArgumentException('$resource must be callable or resource.');
        }

        $this->resource = $resource;
    }

    /**
     * @param resource|callable():void $resource
     */
    public static function attachment($resource, string $filename): self
    {
        return (new self($resource))->asAttachment($filename);
    }

    /**
     * @param resource|callable():void $resource
     */
    public static function inline($resource, string $filename): self
    {
        return (new self($resource))->asInline($filename);
    }

    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        $headers = [];
        $callback = $this->resource;

        if ($contentType = $this->contentType($container)) {
            $headers['Content-Type'] = $contentType;
        }

        if (\is_resource($callback)) {
            $callback = function() {
                fpassthru($this->resource);
            };

            if ($size = \fstat($this->resource)['size'] ?? false) {
                $headers['Content-Length'] = $size;
            }
        }

        return parent::__invoke($request, $container, new StreamedResponse($callback, headers: $headers));
    }

    public function asAttachment(string $filename): self
    {
        return $this->addDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
    }

    public function asInline(string $filename): self
    {
        return $this->addDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
    }

    private function addDisposition(string $type, string $filename): self
    {
        if (!$this->type) {
            $this->type = \pathinfo($filename, \PATHINFO_EXTENSION);
        }

        return $this->withResponse(function(Response $response) use ($type, $filename) {
            $response->headers->set(
                'Content-Disposition',
                $response->headers->makeDisposition($type, $filename)
            );
        });
    }

    private function contentType(ContainerInterface $container): ?string
    {
        if (!$this->type) {
            return null;
        }

        if (\str_contains($this->type, '/')) {
            return $this->type;
        }

        if (!\interface_exists(MimeTypesInterface::class) || !$container->has(MimeTypesInterface::class)) {
            throw new \LogicException('symfony/mime is required guess mime types from extensions. Try running "composer require symfony/mime".');
        }

        return $container->get(MimeTypesInterface::class)->getMimeTypes($this->type)[0] ?? null;
    }
}
