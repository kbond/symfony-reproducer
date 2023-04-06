<?php

namespace App\Message;

use App\Messenger\Monitor\Stamp\Tag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Tag('tag1')]
final class MessageA implements Async
{
    public function __construct(public readonly string $message, public readonly bool $throw = false)
    {
    }
}
