<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class ScheduleSubscriber implements EventSubscriberInterface
{
    public function onEvent(AbstractWorkerMessageEvent|SendMessageToTransportsEvent $event): void
    {
        //dump($event::class, $event->getEnvelope()->getMessage()::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onEvent',
            WorkerMessageReceivedEvent::class => 'onEvent',
            SendMessageToTransportsEvent::class => 'onEvent',
        ];
    }
}
