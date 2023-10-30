<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use App\Entity\Pet;
use App\Entity\User;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
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
use Symfony\Component\Validator\ConstraintViolationList;
use ApiPlatform\Validator\ValidatorInterface;

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
    private PetListProvider $provider;

    public function setUp(): void
    {
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->operation = $this->createMock(Operation::class);
        $this->paginator = $this->createMock(Paginator::class);

        $this->provider = new PetListProvider($this->petRepository, $this->serializer, $this->validator, $this->petTransformer);
    }

    /**
     * @dataProvider successCasesProvider
     * @covers  PetListProvider::provide
     */
    public function testProvideWithValidData(
        array         $expectedPets,
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
            ->with($expectedPetRequestDto);

        $this->petRepository->expects($this->once())
            ->method('findByFilter')
            ->with($expectedPetRequestDto)
            ->willReturn($this->paginator);

        $iterator = new \ArrayIterator($expectedPets);
        $this->paginator->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $withConsecutive = [];
        foreach ($expectedPets as $expectedPet) {
            $withConsecutive[] = [$expectedPet];
        }

        $expectedPetDtoArray = [];
        foreach ($resultPetDto as $resultDto) {
            $expectedPetDtoArray[] = $resultDto;
        }

        $this->petTransformer->expects($this->exactly(count($expectedPets)))
            ->method('toDto')
            ->withConsecutive(...$withConsecutive)
            ->willReturnOnConsecutiveCalls(...$expectedPetDtoArray);

        $result = $this->provider->provide($this->operation, [], $context);

        $this->assertEquals($resultPetDto, $result);

    }

    public function successCasesProvider(): array
    {
        $expectedPetCat = (new Pet())
            ->setName('Cat')
            ->setDescription('Very lazy')
            ->setCreatedAt(new DateTime('2023-09-10 17:45:23'))
            ->setUpdatedAt(new DateTime('2023-10-11 18:48:02'))
            ->setCreatedBy(23)
            ->setUpdatedBy(1)
            ->setOwner((new User())->setName('john'));

        $expectedPetDog = (new Pet())
            ->setName('Dog')
            ->setDescription('Good')
            ->setCreatedAt(new DateTime('2023-09-12 15:30:15'))
            ->setUpdatedAt(new DateTime('2023-10-15 16:20:40'))
            ->setCreatedBy(42)
            ->setUpdatedBy(2)
            ->setOwner((new User())->setName('ben'));

        $expectedPetFrog = (new Pet())
            ->setName('Frog')
            ->setDescription('Green')
            ->setCreatedAt(new DateTime('2023-09-14 10:10:10'))
            ->setUpdatedAt(new DateTime('2023-10-19 09:45:55'))
            ->setCreatedBy(7)
            ->setUpdatedBy(3)
            ->setOwner((new User())->setName('frank'));

        $expectedPetDtoCat = (new PetDto())
            ->setName('Cat')
            ->setDescription('Very lazy')
            ->setCreatedAt(new DateTime('2023-09-10 17:45:23'))
            ->setUpdatedAt(new DateTime('2023-10-11 18:48:02'))
            ->setCreatedBy(23)
            ->setUpdatedBy(1)
            ->setOwner((new User())->setName('john'));

        $expectedPetDtoDog = (new PetDto())
            ->setName('Dog')
            ->setDescription('Good')
            ->setCreatedAt(new DateTime('2023-09-12 15:30:15'))
            ->setUpdatedAt(new DateTime('2023-10-15 16:20:40'))
            ->setCreatedBy(42)
            ->setUpdatedBy(2)
            ->setOwner((new User())->setName('ben'));

        $expectedPetDtoFrog = (new PetDto())
            ->setName('Frog')
            ->setDescription('Green')
            ->setCreatedAt(new DateTime('2023-09-14 10:10:10'))
            ->setUpdatedAt(new DateTime('2023-10-19 09:45:55'))
            ->setCreatedBy(7)
            ->setUpdatedBy(3)
            ->setOwner((new User())->setName('frank'));

        $expectedPetRequestDto = new PetRequestDto();
        $expectedParameters = null;
        $expectedParameters2 = ['name' => 'bird'];
        $expectedParameters3 = ['name' => 'dogф'];
        $resultPetDto1 = [$expectedPetDtoCat];
        $resultPetDto2 = [$expectedPetDtoCat, $expectedPetDtoDog];
        $resultPetDto3 = [$expectedPetDtoFrog];

        $expectedPets = [$expectedPetCat, $expectedPetDog];

        return [
            [[$expectedPetCat], $expectedPetDtoCat, $expectedPetRequestDto, $expectedParameters, $resultPetDto1],
            [$expectedPets, $expectedPetDtoDog, $expectedPetRequestDto, $expectedParameters2, $resultPetDto2],
            [[$expectedPetFrog], $expectedPetDtoFrog, $expectedPetRequestDto, $expectedParameters3, $resultPetDto3],
        ];
    }

    /**
     * @dataProvider successCasesProvider
     * @covers       PetListProvider::provide
     * @param array $expectedPet
     * @param PetDto $expectedPetDto
     * @param PetRequestDto $expectedPetRequestDto
     * @param $expectedParameters
     * @return void
     */
    public function testThrowsException(
        array $expectedPet,
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

        $this->expectException(ValidationException::class);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($expectedPetRequestDto)
            ->willThrowException(new ValidationException(new ConstraintViolationList()));

        $this->petRepository->expects($this->never())
            ->method('findByFilter');

        $this->paginator->expects($this->never())
            ->method('getIterator');

        $this->petTransformer->expects($this->never())
            ->method('toDto');

        $this->provider->provide($this->operation, [], $context);
    }

    /**
     * @dataProvider successCasesProvider
     * @covers  PetListProvider::provide
     */
    public function testNoPetsFound(
        array           $expectedPet,
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
            ->with($expectedPetRequestDto);

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