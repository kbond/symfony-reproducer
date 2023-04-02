<?php

namespace App\Messenger\Monitor\Storage;

use App\Entity\StoredMessage;
use App\Messenger\Monitor\Storage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMStorage implements Storage
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

    public function averageWaitTime(FilterBuilder|Filter $filter): ?float
    {
        return $this->queryBuilderFor($filter)
            ->select('AVG(m.receivedAt - m.dispatchedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function averageHandlingTime(FilterBuilder|Filter $filter): ?float
    {
        return $this->queryBuilderFor($filter)
            ->select('AVG(m.handledAt - m.receivedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function count(FilterBuilder|Filter $filter): int
    {
        return $this->queryBuilderFor($filter)
            ->select('COUNT(m.handledAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function queryBuilderFor(FilterBuilder|Filter $filter): QueryBuilder
    {
        if ($filter instanceof FilterBuilder) {
            $filter = $filter->build();
        }

        $qb = $this->registry
            ->getRepository($this->storedMessageClass)
            ->createQueryBuilder('m')
            ->where('m.handledAt >= :from')
            ->setParameter('from', $filter->from)
        ;

        if ($filter->to) {
            $qb->andWhere('m.handledAt <= :to')->setParameter('to', $filter->to);
        }

        if ($filter->messageType) {
            $qb->andWhere('m.class = :class')->setParameter('class', $filter->messageType);
        }

        if ($filter->receiver) {
            $qb->andWhere('m.receiver = :receiver')->setParameter('receiver', $filter->receiver);
        }

        match($filter->status ?? null) {
            Filter::STATUS_SUCCESS => $qb->andWhere('m.error IS NULL'),
            Filter::STATUS_FAILED => $qb->andWhere('m.error IS NOT NULL'),
            null => null,
        };

        foreach ($filter->tags() as $i => $tag) {
            $qb->andWhere('m.tags LIKE :tag'.$i)->setParameter('tag'.$i, '%'.$tag.'%');
        }

        return $qb;
    }
}
