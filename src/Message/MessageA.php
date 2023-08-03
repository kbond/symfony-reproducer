<?php

namespace App\Message;

use Zenstruck\Messenger\Monitor\Stamp\Tag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Tag('message-a')]
final class MessageA
{
    public function __construct(public readonly string $message, public readonly bool $throw = false)
    {
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
