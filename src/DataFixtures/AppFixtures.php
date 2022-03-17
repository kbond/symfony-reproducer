<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $c1 = CategoryFactory::createOne(['title' => 'English Category 1']);

        PostFactory::createOne(['title' => 'English Title 1', 'description' => 'English Description 1', 'category' => $c1]);
        PostFactory::createOne(['title' => 'English Title 2', 'description' => 'English Description 2', 'category' => $c1]);
    }
}
