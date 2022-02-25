<?php

namespace App\ORM;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HydrationTracker implements ResetInterface
{
    private array $byEntity = [];
    private array $byHydrator = [];

    public function __construct(private Stopwatch $stopwatch)
    {
    }

    public function start(): void
    {
        $this->stopwatch->start('hydration', 'doctrine');
    }

    public function stop(int $number, ResultSetMapping $rst, AbstractHydrator $hydrator): void
    {
        $this->stopwatch->stop('hydration');

        if (!isset($this->byHydrator[$hydrator::class])) {
            $this->byHydrator[$hydrator::class] = 0;
        }

        $this->byHydrator[$hydrator::class] += $number;

        foreach ($rst->getAliasMap() as $class) {
            if (!isset($this->byEntity[$class])) {
                $this->byEntity[$class] = 0;
            }

            $this->byEntity[$class] += $number;
        }
    }

    public function byEntity(): array
    {
        return $this->byEntity;
    }

    public function byHydrator(): array
    {
        return $this->byHydrator;
    }

    public function reset(): void
    {
        $this->byEntity = $this->byHydrator = [];
    }
}
