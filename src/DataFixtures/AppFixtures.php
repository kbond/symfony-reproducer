<?php

namespace App\DataFixtures;

use App\Factory\ImageFactory;
use App\Factory\PropertyFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $property1 = PropertyFactory::createOne();
        $property2 = PropertyFactory::createOne();
        $property3 = PropertyFactory::createOne();

        ImageFactory::createOne(['data' => 'image1', 'type' => 'foo', 'sort' => 0, 'property' => $property1]);
        ImageFactory::createOne(['data' => 'image2', 'type' => 'foo', 'sort' => 30, 'property' => $property1]);
        ImageFactory::createOne(['data' => 'image3', 'type' => 'bar', 'sort' => 20, 'property' => $property1]);
        ImageFactory::createOne(['data' => 'image4', 'type' => 'bar', 'sort' => 40, 'property' => $property1]);

        ImageFactory::createOne(['data' => 'image5', 'type' => 'foo', 'sort' => 0, 'property' => $property2]);
        ImageFactory::createOne(['data' => 'image6', 'type' => 'foo', 'sort' => 30, 'property' => $property2]);
        ImageFactory::createOne(['data' => 'image8', 'type' => 'bar', 'sort' => 25, 'property' => $property2]);
        ImageFactory::createOne(['data' => 'image7', 'type' => 'bar', 'sort' => 20, 'property' => $property2]);

        ImageFactory::createOne(['data' => 'image9', 'type' => 'foo', 'sort' => 0, 'property' => $property3]);
    }
}
