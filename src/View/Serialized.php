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
    protected function __construct(private mixed $data, private array $context = [], private ?string $format = null)
    {
        parent::__construct();
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
