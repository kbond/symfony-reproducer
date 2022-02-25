<?php

namespace App\ORM;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TraceableHydrator extends AbstractHydrator
{
    public function __construct(private AbstractHydrator $inner, private HydrationTracker $tracker)
    {
    }

    public function iterate($stmt, $resultSetMapping, array $hints = []): IterableResult
    {
        return $this->inner->iterate($stmt, $resultSetMapping, $hints);
    }

    public function toIterable($stmt, ResultSetMapping $resultSetMapping, array $hints = []): iterable
    {
        return $this->inner->toIterable($stmt, $resultSetMapping, $hints);
    }

    public function hydrateAll($stmt, $resultSetMapping, array $hints = []): array
    {
        $this->tracker->start();

        $results = $this->inner->hydrateAll($stmt, $resultSetMapping, $hints);

        $this->tracker->stop(\count($results), $resultSetMapping, $this->inner);

        return $results;
    }

    public function hydrateRow(): array|bool
    {
        return $this->inner->hydrateRow();
    }

    public function onClear($eventArgs): void
    {
        $this->inner->onClear($eventArgs);
    }

    protected function hydrateAllData(): array
    {
        throw new \BadMethodCallException('This should never be called.');
    }
}
