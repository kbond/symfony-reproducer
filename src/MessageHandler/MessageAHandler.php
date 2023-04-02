<?php

namespace App\MessageHandler;

use App\Message\MessageA;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsMessageHandler]
final class MessageAHandler
{
    public function __invoke(MessageA $message): string
    {
        sleep(\random_int(0, 5));

        if ($message->throw) {
            throw new \RuntimeException($message->message);
        }

        return $message->message;
    }
}
