<?php

namespace App\Tests\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Processor\UserDeleteProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserDeleteProcessorTest extends TestCase
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private Operation $operation;
    private UserDeleteProcessor $processor;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->operation = $this->createMock(Operation::class);

        $this->processor = new UserDeleteProcessor($this->userRepository, $this->entityManager);
    }

    /**
     * @test
     * @covers UserDeleteProcessor::process
     */
    public function processorProperties(): void
    {
        $userId = 1;
        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($user);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->processor->process(null, $this->operation, ['id' => $userId]);
    }
}