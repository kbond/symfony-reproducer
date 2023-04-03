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

    public function averageWaitTime(Filter $filter): ?float
    {
        return $this->queryBuilderFor($filter)
            ->select('AVG(m.receivedAt - m.dispatchedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function averageHandlingTime(Filter $filter): ?float
    {
        return $this->queryBuilderFor($filter)
            ->select('AVG(m.handledAt - m.receivedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function count(Filter $filter): int
    {
        return $this->queryBuilderFor($filter)
            ->select('COUNT(m.handledAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function queryBuilderFor(Filter $filter): QueryBuilder
    {
        [$from, $to, $status, $messageType, $receiver, $tags] = \array_values($filter->toArray());

        $qb = $this->registry
            ->getRepository($this->storedMessageClass)
            ->createQueryBuilder('m')
        ;

        if ($from) {
            $qb->andWhere('m.handledAt >= :from')->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('m.handledAt <= :to')->setParameter('to', $to);
        }

        if ($messageType) {
            $qb->andWhere('m.class = :class')->setParameter('class', $messageType);
        }

        if ($receiver) {
            $qb->andWhere('m.receiver = :receiver')->setParameter('receiver', $receiver);
        }

        match($status) {
            Filter::SUCCESS => $qb->andWhere('m.error IS NULL'),
            Filter::FAILED => $qb->andWhere('m.error IS NOT NULL'),
            null => null,
        };

        foreach ($tags as $i => $tag) {
            $qb->andWhere('m.tags LIKE :tag'.$i)->setParameter('tag'.$i, '%'.$tag.'%');
        }

        return $qb;
    }
}
