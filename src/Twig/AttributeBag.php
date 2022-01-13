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

    public function prepend(array $with): self
    {
        foreach ($this->attributes as $key => $value) {
            $with[$key] = isset($with[$key]) ? "{$with[$key]} {$value}" : $value;
        }

        return new self($with);
    }

    public function append(array $with): self
    {
        foreach ($this->attributes as $key => $value) {
            $with[$key] = isset($with[$key]) ? "{$value} {$with[$key]}" : $value;
        }

        return new self($with);
    }

    public function only(string ...$keys): self
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $keys, true)) {
                $attributes[$key] = $value;
            }
        }

        return new self($attributes);
    }

    public function reject(string ...$keys): self
    {
        $clone = clone $this;

        foreach ($keys as $key) {
            unset($clone->attributes[$key]);
        }

        return $clone;
    }
}
