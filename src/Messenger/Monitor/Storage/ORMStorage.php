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

    public function find(mixed $id): ?StoredMessage
    {
        return $this->repository()->find($id);
    }

    public function filter(Specification $specification): Collection
    {
        return new Result($this->queryBuilderFor($specification));
    }

    public function save(Envelope $envelope, ?\Throwable $exception = null): void
    {
        $om = $this->registry->getManagerForClass($this->storedMessageClass) ?? throw new \LogicException('No object manager for class.');
        $object = $this->storedMessageClass::create($envelope, $exception);

        $om->persist($object);
        $om->flush();
    }

    public function averageWaitTime(Specification $specification): ?float
    {
        return $this->queryBuilderFor($specification)
            ->select('AVG(m.receivedAt - m.dispatchedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function averageHandlingTime(Specification $specification): ?float
    {
        return $this->queryBuilderFor($specification)
            ->select('AVG(m.handledAt - m.receivedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function count(Specification $specification): int
    {
        return $this->queryBuilderFor($specification)
            ->select('COUNT(m.handledAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function repository(): EntityRepository
    {
        return $this->registry->getRepository($this->storedMessageClass);
    }

    private function queryBuilderFor(Specification $specification): QueryBuilder
    {
        [$from, $to, $status, $messageType, $transport, $tags] = \array_values($specification->toArray());

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
            Specification::SUCCESS => $qb->andWhere('m.error IS NULL'),
            Specification::FAILED => $qb->andWhere('m.error IS NOT NULL'),
            null => null,
        };

        foreach ($tags as $i => $tag) {
            $qb->andWhere('m.tags LIKE :tag'.$i)->setParameter('tag'.$i, '%'.$tag.'%');
        }

        return $qb;
    }
}
