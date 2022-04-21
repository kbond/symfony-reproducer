<?php

namespace App\View;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Serialized extends View
{
    protected ?string $format = null;

    final protected function __construct(private mixed $data, private array $context = [])
    {
        parent::__construct();
    }

    final public static function ok(mixed $data, array $context = []): static
    {
        return new static($data, $context);
    }

    final public static function created(mixed $data, array $context = []): static
    {
        return (new static($data, $context))->withStatus(Response::HTTP_CREATED);
    }

    final public static function accepted(mixed $data, array $context = []): static
    {
        return (new static($data, $context))->withStatus(Response::HTTP_ACCEPTED);
    }

    final public static function unprocessable(mixed $data, array $context = []): static
    {
        return (new static($data, $context))->withStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @internal
     */
    final public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        // get format in order: 1. manually set, _format request attribute, request accept header
        $format = $this->format ?? $request->getRequestFormat(null) ?? $request->getPreferredFormat();
        $serializer = $container->has(SerializerInterface::class) ? $container->get(SerializerInterface::class) : null;
        $context = $this->context;

        if ('json' === $format && !$serializer) {
            if ($context) {
                throw new \LogicException('Cannot use serializer context without serializer. Try running "composer require serializer".');
            }

            return parent::__invoke($request, $container, new JsonResponse($this->data));
        }

        if (!$serializer) {
            throw new \LogicException(\sprintf('The serializer is required to use "%s". Try running "composer require serializer".', self::class));
        }

        if ('json' === $format) {
            $context['json_encode_options'] = JsonResponse::DEFAULT_ENCODING_OPTIONS;
        }

        $serialized = $serializer->serialize($this->data, $format, $context);
        $mimeType = $request->getMimeType($format);
        $headers = $mimeType ? ['Content-Type' => $request->getMimeType($format)] : [];

        if ('json' === $format) {
            return parent::__invoke($request, $container, new JsonResponse(
                $serialized,
                headers: $headers,
                json: true,
            ));
        }

        return parent::__invoke($request, $container, new Response($serialized, headers: $headers));
    }
}
