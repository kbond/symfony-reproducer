<?php

namespace App\View;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Json extends Serialized
{
    protected ?string $format = 'json';

    /**
     * Sets the JSONP callback.
     */
    public function withCallback(string $callback): self
    {
        return $this->withResponse(fn(JsonResponse $r) => $r->setCallback($callback));
    }
}
