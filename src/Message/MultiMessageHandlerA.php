<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsMessageHandler]
final class MultiMessageHandlerA
{
    public function __invoke(MultiMessage $message): string
    {
        return 'a';
    }
}
