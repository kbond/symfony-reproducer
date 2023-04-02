<?php

namespace App\Messenger\Monitor\Twig\Component;

use App\Messenger\Monitor\Worker\Monitor;
use App\Messenger\Monitor\Worker\Status;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('worker_status_widget')]
final class WorkerStatusWidget
{
    use DefaultActionTrait;

    public function __construct(private Monitor $monitor)
    {
    }

    /**
     * @return array<int,Status>
     */
    public function workers(): array
    {
        return $this->monitor->all();
    }
}
