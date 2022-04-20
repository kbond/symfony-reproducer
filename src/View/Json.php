<?php

namespace App\View;

use App\View;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    }

    /**
     * Sets the JSONP callback.
     */
    public function withCallback(string $callback): self
    {
        return $this->withResponse(fn(JsonResponse $r) => $r->setCallback($callback));
    }

    /**
     * @internal
     */
    public function __invoke(?SerializerInterface $serializer = null): JsonResponse
    {
        if ($this->context && !$serializer) {
            throw new \LogicException('Cannot use serializer context without serializer. Try running "composer require serializer".');
        }

        return $this->manipulate($this->createResponse($serializer));
    }

    private function createResponse(?SerializerInterface $serializer): JsonResponse
    {
        if (!$serializer) {
            return new JsonResponse($this->data);
        }

        return new JsonResponse(
            $serializer->serialize($this->data, 'json', [
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
                ...$this->context
            ]),
            json: true,
        );
    }
}
