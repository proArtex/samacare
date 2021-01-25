<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User('user_1', 'sadasdGJHVhgcgfxnbKTY561');
        $user2 = new User('user_2', 'sadasdGJHVhgcgfxnbKTY562');
        $user3 = new User('user_3', 'sadasdGJHVhgcgfxnbKTY563');

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);

        $manager->flush();
    }
}
