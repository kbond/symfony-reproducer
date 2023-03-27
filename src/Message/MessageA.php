<?php

namespace App\Message;

use App\Messenger\Monitor\Stamp\TagStamp;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[TagStamp('tag1')]
final class MessageA implements Async
{
    public function __construct(public readonly string $message, public readonly bool $throw = false)
    {
    }
}
