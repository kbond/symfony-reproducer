<?php

namespace App\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TraceableEntityManager extends EntityManager
{
    private HydrationTracker $tracker;

    public static function create($connection, Configuration $config, ?EventManager $eventManager = null): self
    {
        if (! $config->getMetadataDriverImpl()) {
            throw MissingMappingDriverImplementation::create();
        }

        $connection = static::createConnection($connection, $config, $eventManager);

        return new self($connection, $config, $connection->getEventManager());
    }

    public function newHydrator($hydrationMode): AbstractHydrator
    {
        return new TraceableHydrator(parent::newHydrator($hydrationMode), $this->tracker);
    }

    public function setHydrationTracker(HydrationTracker $tracker)
    {
        $this->tracker = $tracker;
    }
}
