<?php

namespace App\ORM;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Internal\Hydration\HydratorFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TraceableHydratorFactory implements HydratorFactory
{
    public function __construct(private HydratorFactory $inner, private HydrationTracker $tracker)
    {
    }

    public function create(EntityManagerInterface $em, Configuration $config, $hydrationMode): AbstractHydrator
    {
        return new TraceableHydrator($this->inner->create($em, $config, $hydrationMode), $this->tracker);
    }
}
