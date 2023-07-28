<?php

namespace App\Message;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MessageB
{
    public function __construct(private string $message)
    {
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
