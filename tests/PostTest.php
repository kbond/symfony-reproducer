<?php

namespace App\Tests;

use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PostTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    /**
     * @test
     */
    public function create_comment(): void
    {
        $comment = CommentFactory::createOne();

        CommentFactory::assert()->count(1);
        PostFactory::assert()->count(1);
        $this->assertSame(PostFactory::first()->getId(), $comment->getPost()->getId());
    }

    /**
     * @test
     */
    public function create_post(): void
    {
        PostFactory::createOne([
            'comments' => CommentFactory::new()->many(5), // TypeError: Cannot assign array to property App\Entity\Post::$comments of type Doctrine\Common\Collections\Collection
        ]);

        PostFactory::assert()->count(1);
        CommentFactory::assert()->count(5);
    }
}
