<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsMessageHandler]
final class MessageAHandler
{
    public function __invoke(MessageA $message): void
    {
    }
}
