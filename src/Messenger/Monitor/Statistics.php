<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Statistics\Snapshot;
use App\Messenger\Monitor\Storage\Specification;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Statistics
{
    public function __construct(private Storage $storage)
    {
    }

    public function snapshot(Specification $specification): Snapshot
    {
        return new Snapshot($this->storage, $specification);
    }

    public function lastHour(): Snapshot
    {
        return $this->snapshot(Specification::lastHour());
    }

    public function lastDay(): Snapshot
    {
        return $this->snapshot(Specification::lastDay());
    }

    public function lastWeek(): Snapshot
    {
        return $this->snapshot(Specification::lastWeek());
    }

    public function lastMonth(): Snapshot
    {
        return $this->snapshot(Specification::lastMonth());
    }
}
