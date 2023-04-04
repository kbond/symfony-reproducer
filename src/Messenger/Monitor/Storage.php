<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Model\StoredMessage;
use App\Messenger\Monitor\Storage\Filter;
use Symfony\Component\Messenger\Envelope;
use Zenstruck\Collection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Storage
{
    public function get(mixed $id): ?StoredMessage;

    /**
     * @return Collection<int,StoredMessage>
     */
    public function find(Filter $filter): Collection;

    public function save(Envelope $envelope, ?\Throwable $exception = null): void;

    public function averageWaitTime(Filter $filter): ?float;

    public function averageHandlingTime(Filter $filter): ?float;

    public function count(Filter $filter): int;
}
