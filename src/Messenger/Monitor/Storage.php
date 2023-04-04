<?php

namespace App\Messenger\Monitor;

use App\Messenger\Monitor\Model\StoredMessage;
use App\Messenger\Monitor\Storage\Specification;
use Symfony\Component\Messenger\Envelope;
use Zenstruck\Collection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Storage
{
    public function find(mixed $id): ?StoredMessage;

    /**
     * @return Collection<int,StoredMessage>
     */
    public function filter(Specification $specification): Collection;

    public function save(Envelope $envelope, ?\Throwable $exception = null): void;

    public function averageWaitTime(Specification $specification): ?float;

    public function averageHandlingTime(Specification $specification): ?float;

    public function count(Specification $specification): int;
}
