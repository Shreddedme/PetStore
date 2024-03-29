<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Model\Dto\UserDto;
use App\Model\Dto\UserRequestDto;
use App\Repository\UserRepository;
use App\Service\Provider\UserListProvider;
use App\Transformer\UserTransformer;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class UserListProviderTest extends TestCase
{
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private UserTransformer $userTransformer;
    private Operation $operation;
    private Paginator $paginator;
    private UserListProvider $provider;
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->userTransformer = $this->createMock(UserTransformer::class);
        $this->operation = $this->createMock(Operation::class);
        $this->paginator = $this->createMock(Paginator::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->provider = new UserListProvider(
            $this->userRepository,
            $this->serializer,
            $this->userTransformer,
            $this->logger,
            $this->validator
        );
    }

    /**
     * @dataProvider successCasesProvider
     * @covers  UserListProvider::provide
     */
    public function testProvideWithValidData(
        array         $expectedUsers,
        UserDto        $expectedUserDto,
        UserRequestDto $expectedUserRequestDto,
        $expectedParameters,
        $resultUserDto
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                UserRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedUserRequestDto);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedUserRequestDto);

        $this->userRepository->expects($this->once())
            ->method('getAllUsers')
            ->with($expectedUserRequestDto)
            ->willReturn($this->paginator);

        $iterator = new \ArrayIterator($expectedUsers);
        $this->paginator->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $withConsecutive = [];
        foreach ($expectedUsers as $expectedUser) {
            $withConsecutive[] = [$expectedUser];
        }

        $expectedUserDtoArray = [];
        foreach ($resultUserDto as $resultDto) {
            $expectedUserDtoArray[] = $resultDto;
        }

        $this->userTransformer->expects($this->exactly(count($expectedUsers)))
            ->method('toDto')
            ->withConsecutive(...$withConsecutive)
            ->willReturnOnConsecutiveCalls(...$expectedUserDtoArray);

        $result = $this->provider->provide($this->operation, [], $context);

        $this->assertEquals($resultUserDto, $result);
    }

    public function successCasesProvider(): array
    {
        $expectedUserJohn = (new User())
            ->setName('John')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"])
            ->setCreatedAt(new DateTime('2023-09-10 17:45:23'))
            ->setUpdatedAt(new DateTime('2023-10-11 18:48:02'))
            ->setCreatedBy(23)
            ->setUpdatedBy(1);
        $expectedUserBen = (new User())
            ->setName('Ben')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"])
            ->setCreatedAt(new DateTime('2023-09-12 15:30:15'))
            ->setUpdatedAt(new DateTime('2023-10-15 16:20:40'))
            ->setCreatedBy(42)
            ->setUpdatedBy(2);
        $expectedUserFrank = (new User())
            ->setName('Frank')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"])
            ->setCreatedAt(new DateTime('2023-09-14 10:10:10'))
            ->setUpdatedAt(new DateTime('2023-10-19 09:45:55'))
            ->setCreatedBy(7)
            ->setUpdatedBy(3);
        $expectedUserDtoJohn = (new UserDto())
            ->setName('John')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"]);
        $expectedUserDtoBen = (new UserDto())
            ->setName('Ben')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"]);
        $expectedUserDtoFrank = (new UserDto())
            ->setName('Frank')
            ->setPassword('123456')
            ->setEmail('example@mail.ru')
            ->setRoles(["ROLE_USER"]);
        $expectedUserRequestDto = new UserRequestDto();
        $expectedParameters = null;
        $expectedParameters2 = ['name' => 'bird'];
        $expectedParameters3 = ['name' => 'dog'];
        $resultUserDto1 = [$expectedUserDtoJohn];
        $resultUserDto2 = [$expectedUserDtoJohn, $expectedUserDtoBen];
        $resultUserDto3 = [$expectedUserDtoFrank];
        $expectedUsers = [$expectedUserJohn, $expectedUserBen];
        return [
            [[$expectedUserJohn], $expectedUserDtoJohn, $expectedUserRequestDto, $expectedParameters, $resultUserDto1],
            [$expectedUsers, $expectedUserDtoBen, $expectedUserRequestDto, $expectedParameters2, $resultUserDto2],
            [[$expectedUserFrank], $expectedUserDtoFrank, $expectedUserRequestDto, $expectedParameters3, $resultUserDto3],
        ];
    }

    /**
     * @dataProvider successCasesProvider
     * @covers       UserListProvider::provide
     * @param array $expectedUser
     * @param UserDto $expectedUserDto
     * @param UserRequestDto $expectedUserRequestDto
     * @param $expectedParameters
     * @return void
     */
    public function testThrowsException(
        array $expectedUser,
        UserDto $expectedUserDto,
        UserRequestDto $expectedUserRequestDto,
        $expectedParameters
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                UserRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedUserRequestDto);

        $this->expectException(ValidationException::class);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedUserRequestDto)
            ->willThrowException(new ValidationException(new ConstraintViolationList()));

        $this->userRepository->expects($this->never())
            ->method('getAllUsers');

        $this->userTransformer->expects($this->never())
            ->method('toDto');

        $this->provider->provide($this->operation, [], $context);
    }

    /**
     * @dataProvider successCasesProvider
     * @covers  UserListProvider::provide
     */
    public function testNoUsersFound(
        array           $expectedUser,
        UserDto        $expectedUserDto,
        UserRequestDto $expectedUserRequestDto,
                        $expectedParameters
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                UserRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedUserRequestDto);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedUserRequestDto);

        $this->userRepository->expects($this->once())
            ->method('getAllUsers')
            ->with($expectedUserRequestDto)
            ->willReturn($this->paginator);

        $iterator = new \ArrayIterator([]);
        $this->paginator->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->userTransformer->expects($this->never())
            ->method('toDto');

        $result = $this->provider->provide($this->operation, [], $context);

        $this->assertEmpty($result);
    }

}