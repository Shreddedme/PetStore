<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private const USER_COUNT = 1000;

    public function load(ObjectManager $manager)
    {
        $userNames = ['John', 'Dave', 'Frank', 'Luke', 'Mike'];

        for ($i = 0; $i < self::USER_COUNT; $i++) {
            $randomUserName = $userNames[array_rand($userNames)];
            $user = new User();
            $user->setName($randomUserName);
            $user->setEmail($randomUserName . $i . '@example.com');
            $user->setPassword(123456);
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['UserFixtures'];
    }
}