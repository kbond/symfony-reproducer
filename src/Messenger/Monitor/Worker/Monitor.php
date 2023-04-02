<?php

namespace App\Messenger\Monitor\Worker;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<Status>
 */
final class Monitor implements \IteratorAggregate, \Countable, EventSubscriberInterface
{
    private int $pid;

    public function __construct(private CacheItemPoolInterface $cache)
    {
        $this->pid = \getmypid();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerStartedEvent::class => 'onStart',
            WorkerStoppedEvent::class => 'onStop',
            WorkerRunningEvent::class => 'onRunning',
            WorkerMessageReceivedEvent::class => 'onReceived',
        ];
    }

    public function onStart(WorkerStartedEvent $event): void
    {
        [$workers, $item] = $this->workers();

        $workers[$this->pid] = new Status($event->getWorker()->getMetadata());

        $item->set($workers);

        $this->cache->save($item);
    }

    public function onStop(): void
    {
        [$workers, $item] = $this->workers();

        unset($workers[$this->pid], $this->status);

        $item->set($workers);

        $this->cache->save($item);
    }

    public function onRunning(): void
    {
        [$workers, $item] = $this->workers();

        $workers[$this->pid]->markIdle();

        $item->set($workers);

        $this->cache->save($item);
    }

    public function onReceived(): void
    {
        [$workers, $item] = $this->workers();

        $workers[$this->pid]->markProcessing();

        $item->set($workers);

        $this->cache->save($item);
    }

    /**
     * @return array<int,Status>
     */
    public function all(): array
    {
        return \iterator_to_array($this);
    }

    public function getIterator(): \Traversable
    {
        [$workers, $item] = $this->workers();
        $cleaned = false;

        foreach ($workers as $pid => $worker) {
            if (false === \posix_getpgid($pid)) {
                $cleaned = true;
                unset($workers[$pid]);

                continue;
            }

            yield $pid => $worker;
        }

        if ($cleaned) {
            $item->set($workers);
            $this->cache->save($item);
        }
    }

    public function count(): int
    {
        return \iterator_count($this);
    }

    /**
     * @return array{0:array<int,Status>,1:ItemInterface}
     */
    private function workers(): array
    {
        $item = $this->cache->getItem('zs.messenger.monitor.workers');

        return [$item->get() ?: [], $item];
    }
}
