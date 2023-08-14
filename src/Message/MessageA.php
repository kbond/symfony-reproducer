<?php

namespace App\Message;

use Zenstruck\Messenger\Monitor\Stamp\TagStamp;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[TagStamp('message-a')]
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
