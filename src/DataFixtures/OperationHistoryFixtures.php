<?php

namespace App\DataFixtures;

use App\Entity\OperationHistory;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
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
        $currentDate = new DateTimeImmutable('now');
        $hasPets = false;

        foreach ($users as $user) {
            $pets = $user->getPet()->toArray();

            if (count($pets) > 0) {
                $hasPets = true;
                break;
            }
        }

        if ($hasPets) {
            while ($operationHistoryCount < self::OPERATION_HISTORY_COUNT) {
                foreach ($users as $user) {
                    $pets = $user->getPet()->toArray();

                    if (count($pets) === 0) {
                        continue;
                    }

                    foreach ($pets as $pet) {
                        $operationHistory = new OperationHistory();
                        $operationHistory->setOperationDate(DateTime::createFromImmutable($currentDate));

                        $currentDate = $currentDate->add(new DateInterval('P2D'));

                        $operationHistory->setPerformedBy($user);
                        $operationHistory->setPet($pet);

                        $manager->persist($operationHistory);

                        $operationHistoryCount++;
                    }
                }
            }
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