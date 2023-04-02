<?php

namespace App\Messenger\Monitor\Twig\Component;

use App\Messenger\Monitor\Statistics;
use App\Messenger\Monitor\Statistics\Snapshot;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('statistics_widget')]
final class StatisticsWidget
{
    use DefaultActionTrait;

    public function __construct(private Statistics $statistics)
    {
    }

    public function snapshot(): Snapshot
    {
        return $this->statistics->lastDay();
    }
}
