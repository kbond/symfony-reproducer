<?php

namespace App\Messenger\Monitor\Storage\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Type
{
    public function __construct(private string $value)
    {
    }

    public static function from(string|object $value): self
    {
        // todo use zenstruck/class-metadata
        return new self(\is_object($value) ? $value::class : $value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function shortName(): string
    {
        return \str_contains($this->value, '\\') ? \substr($this->value, \strrpos($this->value, '\\') + 1) : $this->value;
    }
}
