<?php

namespace App\Tests\UseCase;

use App\Entity\Pet;
use App\Entity\User;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Service\Pet\PetUseCase;
use App\Transformer\PetTransformer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class PetUseCaseTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PetRepository $petRepository;
    private Security $security;
    private PetTransformer $petTransformer;
    private PetUseCase $petUseCase;
    private User $user;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->user = $this->createMock(User::class);

        $this->petUseCase = new PetUseCase($this->entityManager, $this->petRepository, $this->security, $this->petTransformer);
    }

    /**
     * @test
     * @covers PetUseCase::create
     */
    public function createPet(): void
    {
        $petDto = new PetDto();
        $pet = new Pet();

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->petTransformer->expects($this->once())
            ->method('toEntity')
            ->with(null, $petDto, $this->user)
            ->willReturn($pet);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($pet);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->with($pet)
            ->willReturn($petDto);

        $resultDto = $this->petUseCase->create($petDto);

        $this->assertEquals($petDto, $resultDto);
    }

    /**
     * @test
     * @covers PetUseCase::find
     */
    public function findPet(): void
    {
        $petId = 1;
        $pet = new Pet();

        $this->petRepository->expects($this->once())
            ->method('find')
            ->with($petId)
            ->willReturn($pet);

        $resultPet = $this->petUseCase->find($petId);

        $this->assertEquals($pet, $resultPet);
    }
    /**
     * @test
     * @covers PetUseCase::getOne
     */
    public function getOnePet(): void
    {
        $pet = new Pet();
        $petDto = new PetDto();

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->with($pet)
            ->willReturn($petDto);

        $resultDto = $this->petUseCase->getOne($pet);

        $this->assertEquals($petDto, $resultDto);
    }

    /**
     * @test
     * @covers PetUseCase::update
     */
    public function updatePet(): void
    {
        $petId = 1;
        $petDto = new PetDto();
        $pet = new Pet();

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->petTransformer->expects($this->once())
            ->method('toEntity')
            ->with($petId, $petDto, $this->user)
            ->willReturn($pet);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($pet);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->with($pet)
            ->willReturn($petDto);

        $resultDto = $this->petUseCase->update($petId, $petDto);

        $this->assertEquals($petDto, $resultDto);
    }

    /**
     * @test
     * @covers PetUseCase::delete
     */
    public function deletePet(): void
    {
        $petId = 1;
        $pet = new Pet();

        $this->petRepository->expects($this->once())
            ->method('find')
            ->with($petId)
            ->willReturn($pet);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($pet);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->petUseCase->delete($petId);
    }
}