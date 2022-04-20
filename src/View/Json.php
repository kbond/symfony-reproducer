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
final class Json extends View
{
    /**
     * @param array<string,mixed> $context
     */
    protected function __construct(private mixed $data, private array $context = [])
    {
        parent::__construct();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        $serializer = $container->has(SerializerInterface::class) ? $container->get(SerializerInterface::class) : null;

        if ($this->context && !$serializer) {
            throw new \LogicException('Cannot use serializer context without serializer. Try running "composer require serializer".');
        }

        if (!$serializer) {
            return parent::__invoke($request, $container, new JsonResponse($this->data));
        }

        return parent::__invoke($request, $container, new JsonResponse(
            $serializer->serialize($this->data, 'json', [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
                ...$this->context
            ]),
            json: true,
        ));
    }

    /**
     * Sets the JSONP callback.
     */
    public function withCallback(string $callback): self
    {
        return $this->withResponse(fn(JsonResponse $r) => $r->setCallback($callback));
    }
}
