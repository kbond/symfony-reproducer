<?php

namespace App\View;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Serialized extends View
{
    private ?string $format = null;

    protected function __construct(private mixed $data, private array $context = [])
    {
        parent::__construct();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        if (!$container->has(SerializerInterface::class)) {
            throw new \LogicException(\sprintf('The serializer is required to use "%s". Try running "composer require serializer".', self::class));
        }

        // get format in order: 1. manually set, _format request attribute, request accept header
        $format = $this->format ?? $request->getRequestFormat(null) ?? $request->getPreferredFormat();
        $mimeType = $request->getMimeType($format);

        return parent::__invoke($request, $container, new Response(
            $container->get(SerializerInterface::class)->serialize($this->data, $format, $this->context),
            headers: $mimeType ? ['Content-Type' => $request->getMimeType($format)] : [],
        ));
    }

    public function as(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function asJson(): self
    {
        return $this->as('json');
    }

    public function asXml(): self
    {
        return $this->as('xml');
    }
}
