<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Statistics\Snapshot;
use App\Messenger\Monitor\Storage\FilterBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Statistics
{
    public function __construct(private Storage $storage)
    {
    }

    public function snapshot(FilterBuilder $filter): Snapshot
    {
        return new Snapshot($this->storage, $filter);
    }

    public function lastHour(): Snapshot
    {
        return $this->snapshot(FilterBuilder::lastHour());
    }

    public function lastDay(): Snapshot
    {
        return $this->snapshot(FilterBuilder::lastDay());
    }

    public function lastWeek(): Snapshot
    {
        return $this->snapshot(FilterBuilder::lastWeek());
    }

    public function lastMonth(): Snapshot
    {
        return $this->snapshot(FilterBuilder::lastMonth());
    }
}
