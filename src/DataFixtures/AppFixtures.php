<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Factory\CategoryFactory;
use App\Factory\PostFactory;
use App\Factory\TranslationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $c1 = CategoryFactory::createOne(['title' => 'English Category 1']);

        $p1 = PostFactory::createOne(['title' => 'English Title 1', 'description' => 'English Description 1', 'category' => $c1]);
        $p2 = PostFactory::createOne(['title' => 'English Title 2', 'description' => 'English Description 2', 'category' => $c1]);
        $p3 = PostFactory::createOne(['title' => 'English Title 3', 'description' => 'English Description 3', 'category' => $c1]);

        TranslationFactory::createOne([
            'locale' => 'fr',
            'object' => 'category',
            'objectId' => $c1->getId(),
            'field' => 'title',
            'value' => 'French Category 1',
        ]);

        TranslationFactory::createOne([
            'locale' => 'fr',
            'object' => Post::class,
            'objectId' => $p1->getId(),
            'field' => 'title',
            'value' => 'English Title 1',
        ]);

        TranslationFactory::createOne([
            'locale' => 'fr',
            'object' => Post::class,
            'objectId' => $p1->getId(),
            'field' => 'description',
            'value' => 'English Description 1',
        ]);

        TranslationFactory::createOne([
            'locale' => 'fr',
            'object' => Post::class,
            'objectId' => $p2->getId(),
            'field' => 'title',
            'value' => 'English Title 2',
        ]);

        TranslationFactory::createOne([
            'locale' => 'de',
            'object' => Post::class,
            'objectId' => $p1->getId(),
            'field' => 'title',
            'value' => 'German Title 1',
        ]);
    }
}
