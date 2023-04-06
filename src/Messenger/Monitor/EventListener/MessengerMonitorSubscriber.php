<?php

namespace App\Messenger\Monitor\EventListener;

use App\Messenger\Monitor\Stamp\MonitorStamp;
use App\Messenger\Monitor\Stamp\Tag;
use App\Messenger\Monitor\Storage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
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
    public function __construct(private readonly Storage $storage)
    {
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
                new Tag(\sprintf('schedule:%s', \substr($event->getReceiverName(), 10))), // remove "scheduler_" prefix
            );
        }

        $stamp->markReceived($event->getReceiverName());
    }

    public function handleSuccess(WorkerMessageHandledEvent $event): void
    {
        if (!$event->getEnvelope()->last(MonitorStamp::class)) {
            return;
        }

        $this->storage->save($event->getEnvelope());
    }

    public function handleFailure(WorkerMessageFailedEvent $event): void
    {
        if (!$event->getEnvelope()->last(MonitorStamp::class)) {
            return;
        }

        $this->storage->save($event->getEnvelope(), $event->getThrowable());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SendMessageToTransportsEvent::class => 'addMonitorStamp',
            WorkerMessageReceivedEvent::class => 'receiveMessage',
            WorkerMessageHandledEvent::class => 'handleSuccess',
            WorkerMessageFailedEvent::class => 'handleFailure',
        ];
    }
}
