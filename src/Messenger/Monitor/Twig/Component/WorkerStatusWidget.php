<?php

namespace App\Messenger\Monitor\Twig\Component;

use App\Messenger\Monitor\WorkerMonitor;
use App\Messenger\Monitor\Worker\WorkerStatus;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('worker_status_widget')]
final class WorkerStatusWidget
{
    use DefaultActionTrait;

    public function __construct(private WorkerMonitor $monitor)
    {
    }

    /**
     * @return array<int,WorkerStatus>
     */
    public function workers(): array
    {
        return $this->monitor->all();
    }
}
