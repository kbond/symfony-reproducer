<?php

namespace App\Messenger\Monitor\Statistics;

use App\Messenger\Monitor\Storage;
use App\Messenger\Monitor\Storage\FilterBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Snapshot
{
    private int $successCount;
    private int $failureCount;
    private float $averageWaitTime;
    private float $averageHandlingTime;
    private int $totalSeconds;

    public function __construct(private Storage $storage, private FilterBuilder $filter)
    {
    }

    public function totalCount(): int
    {
        return $this->successCount() + $this->failureCount();
    }

    public function successCount(): int
    {
        return $this->successCount = $this->storage->count($this->filter->successes());
    }

    public function failureCount(): int
    {
        return $this->failureCount = $this->storage->count($this->filter->failures());
    }

    public function failRate(): float
    {
        return $this->totalCount() ? $this->failureCount() / $this->totalCount() : 0;
    }

    public function averageWaitTime(): float
    {
        return $this->averageWaitTime = $this->storage->averageWaitTime($this->filter) ?? 0.0;
    }

    public function averageHandlingTime(): float
    {
        return $this->averageHandlingTime = $this->storage->averageHandlingTime($this->filter) ?? 0.0;
    }

    public function averageTotalProcessingTime(): float
    {
        return $this->averageWaitTime() + $this->averageHandlingTime();
    }

    /**
     * @param positive-int $divisor
     */
    public function handledPer(int $divisor): float
    {
        $interval = $this->totalSeconds() / $divisor;

        return $this->totalCount() / $interval;
    }

    public function handledPerMinute(): float
    {
        return $this->handledPer(60);
    }

    public function handledPerHour(): float
    {
        return $this->handledPer(60 * 60);
    }

    public function handledPerDay(): float
    {
        return $this->handledPer(60 * 60 * 24);
    }

    private function totalSeconds(): int
    {
        if (isset($this->totalSeconds)) {
            return $this->totalSeconds;
        }

        $filter = $this->filter->build();
        $to = $filter->to ?? new \DateTimeImmutable();

        return $this->totalSeconds = \abs($to->getTimestamp() - $filter->from->getTimestamp());
    }
}
