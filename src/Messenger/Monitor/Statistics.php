<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Statistics\Snapshot;
use App\Messenger\Monitor\Storage\Filter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Statistics
{
    public function __construct(private Storage $storage)
    {
    }

    public function snapshot(Filter $filter): Snapshot
    {
        return new Snapshot($this->storage, $filter);
    }

    public function lastHour(): Snapshot
    {
        return $this->snapshot(Filter::lastHour());
    }

    public function lastDay(): Snapshot
    {
        return $this->snapshot(Filter::lastDay());
    }

    public function lastWeek(): Snapshot
    {
        return $this->snapshot(Filter::lastWeek());
    }

    public function lastMonth(): Snapshot
    {
        return $this->snapshot(Filter::lastMonth());
    }
}
