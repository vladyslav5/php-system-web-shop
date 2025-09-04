<?php

namespace App\DataFixtures;

use App\Entity\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
        ];

        foreach ($statuses as $name) {
            $status = new OrderStatus();
            $status->setName($name);
            $manager->persist($status);
        }

        $manager->flush();
    }
}
