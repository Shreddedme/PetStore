<?php

namespace App\DataFixtures;

use App\Entity\OperationHistory;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class OperationHistoryFixtures extends Fixture implements FixtureGroupInterface
{
    private const OPERATION_HISTORY_COUNT = 50000;

    public function __construct(
        private UserRepository $userRepository,
    )
    {}

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        $operationHistoryCount = 0;
        $currentDate = new DateTime('now');
        $userHasPets = false;

        foreach ($users as $user) {
            if (count($user->getPet()->toArray()) > 0) {
                $userHasPets = true;
                break;
            }
        }

        if ($userHasPets) {
                while ($operationHistoryCount < self::OPERATION_HISTORY_COUNT) {
                    $user = $users[array_rand($users)];
                    $pets = $user->getPet()->toArray();
                    if (count($pets) > 0) {
                        $operationHistory = new OperationHistory();
                        $pet = $pets[array_rand($pets)];

                        $operationHistory->setOperationDate(clone $currentDate);
                        $currentDate->add(new DateInterval('P2D'));

                        $operationHistory->setPerformedBy($user);
                        $operationHistory->setPet($pet);
                        $manager->persist($operationHistory);
                        $operationHistoryCount++;
                    }
                }

                $manager->flush();
            }
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