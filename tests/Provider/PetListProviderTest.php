<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use App\Entity\Pet;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Model\Dto\PetRequestDto;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Service\Provider\PetListProvider;
use App\Transformer\PetTransformer;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @coversDefaultClass PetListProvider
 */
class PetListProviderTest extends TestCase
{
    private PetRepository $petRepository;
    private Serializer $serializer;
    private ValidatorInterface $validator;
    private PetTransformer $petTransformer;
    private Operation $operation;
    private Paginator $paginator;
    private ConstraintViolationListInterface $constraintViolationListInterface;
    private PetListProvider $provider;
    private ConstraintViolation $violation;
    public function setUp(): void
    {
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->operation = $this->createMock(Operation::class);
        $this->paginator = $this->createMock(Paginator::class);
        $this->constraintViolationListInterface = $this->createMock(ConstraintViolationListInterface::class);
        $this->violation = $this->createMock(ConstraintViolation::class);

        $this->provider = new PetListProvider($this->petRepository, $this->serializer, $this->validator, $this->petTransformer);
    }

    /**
     * @dataProvider dataProvider
     * @covers  PetListProvider::provide
     ** @throws ValidationException
     */
    public function testProvideWithValidData(
        Pet           $expectedPet,
        PetDto        $expectedPetDto,
        PetRequestDto $expectedPetRequestDto,
                      $expectedParameters,
                      $resultPetDto
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                PetRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedPetRequestDto);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedPetRequestDto)
            ->willReturn($this->constraintViolationListInterface);

        $this->petRepository->expects($this->once())
            ->method('findByFilter')
            ->with($expectedPetRequestDto)
            ->willReturn($this->paginator);

        $iterator = new \ArrayIterator([$expectedPet]);
        $this->paginator->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->with($expectedPet)
            ->willReturn($expectedPetDto);

        $result = $this->provider->provide($this->operation, [], $context);

        $this->assertEquals($resultPetDto, $result);
    }

    public function dataProvider(): array
    {
        $expectedPet = (new Pet())
            ->setName('Cat')
            ->setDescription('Very lazy')
            ->setCreatedAt(new DateTime('2023-09-10 17:45:23'))
            ->setUpdatedAt(new DateTime('2023-10-11 18:48:02'))
            ->setCreatedBy(23)
            ->setUpdatedBy(1)
            ->setOwner((new User())->setName('john'));

        $expectedPetDto = (new PetDto())
            ->setName('Cat')
            ->setDescription('Very lazy')
            ->setCreatedAt(new DateTime('2023-09-10 17:45:23'))
            ->setUpdatedAt(new DateTime('2023-10-11 18:48:02'))
            ->setCreatedBy(23)
            ->setUpdatedBy(1)
            ->setOwner((new User())->setName('john'));

        $expectedPetRequestDto = new PetRequestDto();
        $expectedParameters = null;
        $expectedParameters2 = ['name' => 'bird'];
        $expectedParameters3 = ['name' => 'dogф'];
        $resultPetDto = [$expectedPetDto];

        return [
            [$expectedPet, $expectedPetDto, $expectedPetRequestDto, $expectedParameters, $resultPetDto],
            [$expectedPet, $expectedPetDto, $expectedPetRequestDto, $expectedParameters2, $resultPetDto],
            [$expectedPet, $expectedPetDto, $expectedPetRequestDto, $expectedParameters3, $resultPetDto],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @covers PetListProvider::provide
     * @return void
     * @throws ValidationException
     */
    public function testThrowsValidationException(
        Pet $expectedPet,
        PetDto $expectedPetDto,
        PetRequestDto $expectedPetRequestDto,
        $expectedParameters
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                PetRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedPetRequestDto);

        $violationList = new ConstraintViolationList();
        $violationList->add($this->violation);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedPetRequestDto)
            ->willReturn($violationList);

        $this->expectException(ValidationException::class);

        $this->petRepository->expects($this->never())
            ->method('findByFilter');

        $this->paginator->expects($this->never())
            ->method('getIterator');

        $this->petTransformer->expects($this->never())
            ->method('toDto');

        $this->provider->provide($this->operation, [], $context);
    }

    /**
     * @dataProvider dataProvider
     * @covers  PetListProvider::provide
     * @throws ValidationException
     */
    public function testNoPetsFound(
        Pet           $expectedPet,
        PetDto        $expectedPetDto,
        PetRequestDto $expectedPetRequestDto,
                      $expectedParameters
    ): void
    {
        $context = ['filters' => $expectedParameters];

        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                $expectedParameters,
                PetRequestDto::class,
                null,
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            )
            ->willReturn($expectedPetRequestDto);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedPetRequestDto)
            ->willReturn($this->constraintViolationListInterface);

        $this->petRepository->expects($this->once())
            ->method('findByFilter')
            ->with($expectedPetRequestDto)
            ->willReturn($this->paginator);

        $iterator = new \ArrayIterator([]);
        $this->paginator->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->petTransformer->expects($this->never())
            ->method('toDto');

        $result = $this->provider->provide($this->operation, [], $context);

        $this->assertEmpty($result);
    }
}