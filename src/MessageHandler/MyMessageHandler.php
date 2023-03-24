<?php

namespace App\MessageHandler;

use App\Message\MyMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MyMessageHandler implements MessageHandlerInterface
{
    public function __invoke(MyMessage $message)
    {
        // do something with your message
    }
}
