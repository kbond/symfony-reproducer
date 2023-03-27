<?php

namespace App\Messenger\Monitor\EventListener;

use App\Entity\StoredMessage;
use App\Messenger\Monitor\Stamp\MonitorStamp;
use App\Messenger\Monitor\Stamp\TagStamp;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Scheduler\Messenger\ScheduledStamp;

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

    public function addMonitorStamp(SendMessageToTransportsEvent $event): void
    {
        $event->setEnvelope($event->getEnvelope()->with(new MonitorStamp()));
    }

    public function receiveMessage(WorkerMessageReceivedEvent $event): void
    {
        if (!$stamp = $event->getEnvelope()->last(MonitorStamp::class)) {
            $event->addStamps($stamp = new MonitorStamp()); // scheduler transport doesn't trigger SendMessageToTransportsEvent
        }

        if (\class_exists(ScheduledStamp::class) && $event->getEnvelope()->last(ScheduledStamp::class)) {
            $event->addStamps(
                new TagStamp('schedule'),
                new TagStamp(\sprintf('schedule:%s', \substr($event->getReceiverName(), 10))), // remove "scheduler_" prefix
            );
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

        $om->persist($object); // todo, should this be done in a separate message to avoid unintended consequences of flush()?
        $om->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SendMessageToTransportsEvent::class => 'addMonitorStamp',
            WorkerMessageReceivedEvent::class => 'receiveMessage',
            WorkerMessageHandledEvent::class => 'persistMessage',
        ];
    }
}
