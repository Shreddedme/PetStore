<?php

namespace App\DataFixtures;

use App\Entity\OperationHistory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class OperationHistoryFixtures extends Fixture implements FixtureGroupInterface
{
    private const OPERATION_HISTORY_COUNT = 1000;

    public function __construct(
        private UserRepository $userRepository,
    )
    {}

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < self::OPERATION_HISTORY_COUNT; $i++) {
            $operationHistory = new OperationHistory();
            $operationHistory->setPerformedBy($users[array_rand($users)]);

            $manager->persist($operationHistory);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['OperationHistoryFixtures'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PetFixtures::class,
        ];
    }
}