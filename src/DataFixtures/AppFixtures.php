<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $activity = new Activity('foo');
        $manager->persist($activity);

        $manager->flush();
        $manager->clear();

        $activity = $manager->getRepository(Activity::class)->findAll()[0];
    }
}
