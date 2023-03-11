<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zenstruck\Collection\Doctrine\EntityRepository;
use Zenstruck\Collection\Doctrine\ForClass;

/**
 * @extends EntityRepository<Category>
 */
#[ForClass(Category::class)]
class CategoryRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry, private UrlGeneratorInterface $generator)
    {
        parent::__construct($registry, Category::class);
    }
}
