<?php

namespace App\DataFixtures;

use App\Entity\Pet;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PetFixtures extends Fixture implements FixtureGroupInterface
{
    private const PET_COUNT = 100;

    public function __construct(
        private UserRepository $userRepository,
    )
    {}

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();
        $petNames = ['Cat', 'Dog', 'Bird', 'Fish', 'Rabbit', 'Frog'];
        $petDescriptions = ['Very lazy', 'Good', 'Quiet', 'Slow and calm', 'Small'];

        for ($i = 0; $i < self::PET_COUNT; $i++) {
            $randomName = $petNames[array_rand($petNames)];
            $randomDescription = $petDescriptions[array_rand($petDescriptions)];
            $randomUser = $users[array_rand($users)];
            $pet = new Pet();
            $pet->setName($randomName);
            $pet->setDescription($randomDescription);
            $pet->setCreatedBy($randomUser->getId());
            $pet->setOwner($randomUser);

            $manager->persist($pet);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
       return ['PetFixtures'];
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}