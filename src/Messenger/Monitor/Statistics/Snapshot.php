<?php

namespace App\Messenger\Monitor\Statistics;

use App\Messenger\Monitor\Model\ProcessedMessage;
use App\Messenger\Monitor\Storage;
use App\Messenger\Monitor\Storage\Specification;
use Zenstruck\Collection;

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

    public function __construct(private readonly Storage $storage, public readonly Specification $specification)
    {
        [$from, $to] = \array_values($this->specification->toArray());

        if (!$from) {
            throw new \InvalidArgumentException(\sprintf('Filter must have a "from" date to use "%s".', __CLASS__));
        }

        $this->totalSeconds = \abs(($to ?? new \DateTimeImmutable())->getTimestamp() - $from->getTimestamp());
    }

    /**
     * @return Collection<int,ProcessedMessage>
     */
    public function messages(): Collection
    {
        return $this->storage->filter($this->specification);
    }

    public function totalCount(): int
    {
        return $this->successCount() + $this->failureCount();
    }

    public function successCount(): int
    {
        return $this->successCount ??= $this->storage->count($this->specification->successes());
    }

    public function failureCount(): int
    {
        return $this->failureCount ??= $this->storage->count($this->specification->failures());
    }

    public function failRate(): float
    {
        return $this->totalCount() ? $this->failureCount() / $this->totalCount() : 0;
    }

    public function averageWaitTime(): float
    {
        return $this->averageWaitTime ??= $this->storage->averageWaitTime($this->specification) ?? 0.0;
    }

    public function averageHandlingTime(): float
    {
        return $this->averageHandlingTime ??= $this->storage->averageHandlingTime($this->specification) ?? 0.0;
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
        $interval = $this->totalSeconds / $divisor;

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
}
