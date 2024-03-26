<?php

namespace App\Tests\UseCase;

use App\Entity\User;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use App\Service\User\UserUseCase;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserUseCaseTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserTransformer $userTransformer;
    private UserUseCase $userUseCase;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userTransformer = $this->createMock(UserTransformer::class);

        $this->userUseCase = new UserUseCase($this->entityManager, $this->userRepository, $this->userTransformer);
    }

    /**
     * @test
     * @covers UserUseCase::create
     */
    public function createUser(): void
    {
        $userDto = new UserDto();
        $user = new User();

        $this->userTransformer->expects($this->once())
            ->method('toEntity')
            ->with(null, $userDto)
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

        $resultDto = $this->userUseCase->create($userDto);

        $this->assertEquals($userDto, $resultDto);
    }

    /**
     * @test
     * @covers UserUseCase::find
     */
    public function findUser(): void
    {
        $userId = 1;
        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $resultUser = $this->userUseCase->find($userId);

        $this->assertEquals($user, $resultUser);
    }

    /**
     * @test
     * @covers UserUseCase::getOne
     */
    public function getOneUser(): void
    {
        $user = new User();
        $userDto = new UserDto();

        $this->userTransformer->expects($this->once())
            ->method('toDto')
            ->with($user)
            ->willReturn($userDto);

        $resultDto = $this->userUseCase->getOne($user);

        $this->assertEquals($userDto, $resultDto);
    }

    /**
     * @test
     * @covers UserUseCase::update
     */
    public function updateUser(): void
    {
        $userId = 1;
        $userDto = new UserDto();
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

        $resultDto = $this->userUseCase->update($userId, $userDto);

        $this->assertEquals($userDto, $resultDto);
    }

    /**
     * @test
     * @covers UserUseCase::delete
     */
    public function deleteUser(): void
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

        $this->userUseCase->delete($userId);
    }
}