<?php

namespace App\Repository;

use App\Entity\Post;
use Zenstruck\Collection\Doctrine\EntityRepository;
use Zenstruck\Collection\Doctrine\ForClass;

/**
 * @extends EntityRepository<Post>
 */
#[ForClass(Post::class)]
class PostRepository extends EntityRepository
{
}
