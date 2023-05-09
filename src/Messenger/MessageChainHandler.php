<?php

namespace App\Messenger;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MessageChainHandler
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[AsMessageHandler]
    public function dispatchNext(MessageChain $chain): void
    {
        [$chain, $next] = $chain->pop();

        if (!$next) {
            return;
        }

        $this->bus->dispatch($next, $chain ? [$chain] : []);
    }

    #[AsEventListener]
    public function onHandled(WorkerMessageHandledEvent $event): void
    {
        if ($chain = $event->getEnvelope()->last(MessageChain::class)) {
            $this->dispatchNext($chain);
        }
    }
}
