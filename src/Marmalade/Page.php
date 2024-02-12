<?php

namespace App\Marmalade;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Page implements \ArrayAccess
{
    /**
     * @internal
     */
    public function __construct(
        public readonly string $path,
        public readonly string $url,
        public readonly string $template,
        public readonly array $metadata = [],
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->metadata[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->metadata[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('Pages are immutable.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Pages are immutable.');
    }
}
