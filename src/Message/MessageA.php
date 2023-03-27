<?php

namespace App\Message;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MessageA
{
    public function __construct(public readonly string $message, public readonly bool $throw = false)
    {
    }
}
