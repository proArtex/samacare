<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User('user_1', 'Random_Token_For_User_01');
        $user2 = new User('user_2', 'Random_Token_For_User_02');
        $user3 = new User('user_3', 'Random_Token_For_User_03');

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);

        $manager->flush();
    }
}
