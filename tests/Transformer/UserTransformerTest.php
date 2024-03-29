<?php

namespace App\Tests\Transformer;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use PHPUnit\Framework\TestCase;

class UserTransformerTest extends TestCase
{
    private UserRepository $userRepository;
    private UserTransformer $userTransformer;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userTransformer = new UserTransformer($this->userRepository);
    }

    /**
     * @test
     * @covers UserTransformer::toEntity
     */
    public function toEntity(): void
    {
        $userId = 1;
        $userDto = (new UserDto())
            ->setName('Test User')
            ->setEmail('test@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_USER']);
        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $resultUser = $this->userTransformer->toEntity($userId, $userDto);

        $this->assertEquals($user, $resultUser);
    }

    /**
     * @test
     * @covers UserTransformer::toDto
     */
    public function toDto(): void
    {
        $user = (new User())
            ->setName('Test User')
            ->setEmail('test@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_USER']);

        $resultDto = $this->userTransformer->toDto($user);

        $this->assertInstanceOf(UserDto::class, $resultDto);
    }

    /**
     * @test
     * @covers UserTransformer::find
     */
    public function findUser(): void
    {
        $userId = 1;
        $user = (new User())
            ->setName('Test User')
            ->setEmail('test@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_USER']);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $resultUser = $this->userTransformer->find($userId);

        $this->assertEquals($user, $resultUser);
    }

    /**
     * @test
     * @covers PetTransformer::find
     */
    public function findPetNotFound(): void
    {
        $userId = 1;

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);

        $this->userTransformer->find($userId);
    }
}