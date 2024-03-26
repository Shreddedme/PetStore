<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use App\Service\Provider\UserProvider;
use App\Transformer\UserTransformer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UserProviderTest extends TestCase
{
    private UserRepository $userRepository;
    private UserTransformer $userTransformer;
    private LoggerInterface $logger;
    private UserProvider $userProvider;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userTransformer = $this->createMock(UserTransformer::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->userProvider = new UserProvider($this->userRepository, $this->userTransformer, $this->logger);
    }

    /**
     * @test
     * @covers UserProvider::provide
     */
    public function provide(): void
    {
        $operation = $this->createMock(Operation::class);
        $user = $this->createMock(User::class);
        $userDto = $this->createMock(UserDto::class);
        $uriVariables = ['id' => 1];

        $this->logger->expects($this->exactly(2))
            ->method('info');

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($uriVariables['id'])
            ->willReturn($user);

        $this->userTransformer->expects($this->once())
            ->method('toDto')
            ->with($user)
            ->willReturn($userDto);

        $result = $this->userProvider->provide($operation, $uriVariables);

        $this->assertEquals($userDto, $result);
    }
}