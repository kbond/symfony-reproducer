<?php

namespace App\Messenger\Monitor\Storage;

use App\Entity\StoredMessage as AppStoredMessage;
use App\Messenger\Monitor\Model\StoredMessage;
use App\Messenger\Monitor\Storage;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;
use Zenstruck\Collection;
use Zenstruck\Collection\Doctrine\ORM\Result;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ORMStorage implements Storage
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly string $storedMessageClass = AppStoredMessage::class, // todo make configurable
    ) {
    }

    public function get(mixed $id): ?StoredMessage
    {
        return $this->repository()->find($id);
    }

    public function find(Filter $filter): Collection
    {
        return new Result($this->queryBuilderFor($filter));
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

    private function repository(): EntityRepository
    {
        return $this->registry->getRepository($this->storedMessageClass);
    }

    private function queryBuilderFor(Filter $filter): QueryBuilder
    {
        [$from, $to, $status, $messageType, $transport, $tags] = \array_values($filter->toArray());

        $qb = $this->repository()->createQueryBuilder('m');

        if ($from) {
            $qb->andWhere('m.handledAt >= :from')->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('m.handledAt <= :to')->setParameter('to', $to);
        }

        if ($messageType) {
            $qb->andWhere('m.class = :class')->setParameter('class', $messageType);
        }

        if ($transport) {
            $qb->andWhere('m.transport = :transport')->setParameter('transport', $transport);
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
