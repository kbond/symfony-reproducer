<?php

namespace App\Twig;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AttributeBag
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(public array $attributes)
    {
    }

    public static function create(array|self $attributes): self
    {
        return $attributes instanceof self ? $attributes : new self($attributes);
    }

    public function __toString(): string
    {
        return \array_reduce(
            \array_keys($this->attributes),
            fn(string $carry, string $key) => \sprintf('%s %s="%s"', $carry, $key, $this->attributes[$key]),
            ''
        );
    }
}
