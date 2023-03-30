<?php

namespace App\Messenger\Monitor\Storage;

use App\Entity\StoredMessage;
use App\Messenger\Monitor\Statistics\Metric;
use App\Messenger\Monitor\Statistics\Metric\Average;
use App\Messenger\Monitor\Statistics\Metric\Calculator as StatisticsCalculator;
use App\Messenger\Monitor\Statistics\Metric\HandledPer;
use App\Messenger\Monitor\Statistics\Metric\RateOfFailure;
use App\Messenger\Monitor\Storage;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMStorage implements Storage, StatisticsCalculator
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $storedMessageClass = StoredMessage::class, // todo make configurable
    ) {
    }

    public function save(Envelope $envelope, ?\Throwable $exception = null): void
    {
        $om = $this->registry->getManagerForClass($this->storedMessageClass) ?? throw new \LogicException('No object manager for class.');
        $object = $this->storedMessageClass::create($envelope, $exception);

        $om->persist($object);
        $om->flush();
    }

    public function calculate(Metric $metric): float
    {
        $repo = $this->registry->getRepository($this->storedMessageClass);

        if (!$repo instanceof EntityRepository) {
            throw new \LogicException(\sprintf('Repository for class "%s" must be an instance of "%s".', $this->storedMessageClass, EntityRepository::class));
        }

        $qb = match($metric::class) {
            Average::class => $repo->createQueryBuilder('r'),
            HandledPer::class => $repo->createQueryBuilder('r'),
            RateOfFailure::class => $repo->createQueryBuilder('r'),
            default => throw new \LogicException(\sprintf('"%s" does not support metric "%s".', self::class, $metric::class)),
        };
    }
}
