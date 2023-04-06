<?php

namespace App\Messenger\Monitor\Storage\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Error
{
    private string $message;

    public function __construct(private string $class, string $message)
    {
        $this->message = \trim($message);
    }

    public function __toString(): string
    {
        return \sprintf('%s: %s', $this->class, $this->message);
    }

    public static function from(\Throwable|string $what): self
    {
        if (\is_string($what)) {
            return new self(...\explode(':', $what, 2));
        }

        return new self($what::class, $what->getMessage());
    }

    public function class(): string
    {
        return $this->class;
    }

    public function message(): string
    {
        return $this->message;
    }
}
