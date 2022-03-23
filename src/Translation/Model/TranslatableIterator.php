<?php

namespace App\Translation\Model;

use App\Translation\TranslatableProvider;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Zenstruck\Collection\ArrayCollection;
use Zenstruck\Collection\Doctrine\Batch\BatchIterator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<int,object>
 */
final class TranslatableIterator implements \IteratorAggregate, \Countable
{
    /**
     * @param class-string[] $classes
     */
    public function __construct(private array $classes, private ManagerRegistry $managerRegistry)
    {
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->classes as $class) {
            yield from BatchIterator::for(
                $this->iteratorFor($this->managerRegistry->getRepository($class)),
                $this->managerRegistry->getManagerForClass($class)
            );
        }
    }

    public function count(): int
    {
        return ArrayCollection::for($this->classes)
            ->map(fn(string $c) => $this->countFor($this->managerRegistry->getRepository($c)))
            ->sum()
        ;
    }

    private function iteratorFor(ObjectRepository $repository): \Traversable
    {
        if ($repository instanceof TranslatableProvider) {
            return $repository->translatableObjects();
        }

        if ($repository instanceof EntityRepository) {
            return $repository->createQueryBuilder('o')->getQuery()->toIterable();
        }

        throw new \LogicException(\sprintf('"%s" must implement "%s" in order to iterate over it\'s objects.', $repository::class, TranslatableProvider::class));
    }

    private function countFor(ObjectRepository $repository): int
    {
        if ($repository instanceof TranslatableProvider) {
            return $repository->translatableObjects()->count();
        }

        if ($repository instanceof \Countable) {
            return \count($repository);
        }

        if ($repository instanceof EntityRepository) {
            return $repository->count([]);
        }

        throw new \LogicException(\sprintf('"%s" must implement "%s" in order to iterate over it\'s objects.', $repository::class, TranslatableProvider::class));
    }
}
