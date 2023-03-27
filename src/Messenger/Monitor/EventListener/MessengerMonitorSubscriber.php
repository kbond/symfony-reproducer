<?php

namespace App\Messenger\Monitor\EventListener;

use App\Entity\StoredMessage;
use App\Messenger\Monitor\Stamp\MonitorStamp;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class MessengerMonitorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $storedMessageClass = StoredMessage::class, // todo make configurable
    ) {
    }

    public function addMonitorStamp(SendMessageToTransportsEvent|WorkerMessageReceivedEvent $event): void
    {
        if ($event->getEnvelope()->last(MonitorStamp::class)) {
            return;
        }

        $event->getEnvelope()->with(new MonitorStamp());
    }

    public function receiveMessage(WorkerMessageReceivedEvent $event): void
    {
        if (!$stamp = $event->getEnvelope()->last(MonitorStamp::class)) {
            return;
        }

        $stamp->markReceived($event->getReceiverName());
    }

    public function persistMessage(WorkerMessageHandledEvent $event): void
    {
        if (!$event->getEnvelope()->last(MonitorStamp::class)) {
            return;
        }

        $object = $this->storedMessageClass::create($event->getEnvelope());
        $om = $this->registry->getManagerForClass($object::class);

        $om->persist($object); // todo, should this be done in a separate message?
        $om->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SendMessageToTransportsEvent::class => 'addMonitorStamp',
            WorkerMessageReceivedEvent::class => [
                ['addMonitorStamp', 10],
                ['receiveMessage']
            ],
            WorkerMessageHandledEvent::class => 'persistMessage',
        ];
    }
}
