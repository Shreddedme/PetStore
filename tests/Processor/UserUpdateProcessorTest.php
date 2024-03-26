<?php

namespace App\Tests\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Service\Processor\UserUpdateProcessor;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserUpdateProcessorTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserTransformer $userTransformer;
    private Operation $operation;
    private UserUpdateProcessor $processor;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userTransformer = $this->createMock(UserTransformer::class);
        $this->operation = $this->createMock(Operation::class);

        $this->processor = new UserUpdateProcessor($this->entityManager, $this->userTransformer);
    }

    /**
     * @test
     * @dataProvider userData
     * @covers UserUpdateProcessor::process
     * @param UserDto $userDto
     * @return void
     * @throws EntityNotFoundException
     */
    public function processorProperties(UserDto $userDto): void
    {
        $userId = 1;
        $user = new User();

        $this->userTransformer->expects($this->once())
            ->method('toEntity')
            ->with($userId, $userDto)
            ->willReturn($user);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->userTransformer->expects($this->once())
            ->method('toDto')
            ->with($user)
            ->willReturn($userDto);

        $resultDto = $this->processor->process($userDto, $this->operation, ['id' => $userId]);

        $this->assertEquals($userDto, $resultDto);
    }

    public function userData(): array
    {
        return [
            [(new UserDto())->setName('John')->setEmail('john@example.com')->setRoles(['ROLE_USER'])],
        ];
    }
}