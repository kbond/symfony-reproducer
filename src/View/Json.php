<?php

namespace App\View;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Json extends Serialized
{
    /**
     * @param array<string,mixed> $context
     */
    protected function __construct(mixed $data, array $context = [])
    {
        parent::__construct($data, $context, 'json');
    }

    /**
     * Sets the JSONP callback.
     */
    public function withCallback(string $callback): self
    {
        return $this->withResponse(fn(JsonResponse $r) => $r->setCallback($callback));
    }
}
