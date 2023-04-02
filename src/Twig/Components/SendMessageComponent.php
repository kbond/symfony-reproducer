<?php

namespace App\Twig\Components;

use App\Message\MessageA;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('send_message')]
final class SendMessageComponent
{
    use DefaultActionTrait;

    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[LiveAction]
    public function dispatch(): void
    {
        foreach (\range(1, 10) as $i) {
            $this->bus->dispatch(new MessageA('From Live Component!', (bool) \random_int(0, 100) < 10));
        }
    }
}
