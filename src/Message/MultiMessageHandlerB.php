<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MultiMessageHandlerB
{
    #[AsMessageHandler]
    public function handle(MultiMessage $message): string
    {
        //throw new \RuntimeException('foo');

        return 'b';
    }
}
