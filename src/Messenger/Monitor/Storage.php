<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Storage\Filter;
use App\Messenger\Monitor\Storage\FilterBuilder;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Storage
{
    public function save(Envelope $envelope, ?\Throwable $exception = null): void;

    public function averageWaitTime(Filter|FilterBuilder $filter): float;

    public function averageHandlingTime(Filter|FilterBuilder $filter): float;

    public function count(Filter|FilterBuilder $filter): int;
}
