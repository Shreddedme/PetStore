<?php

namespace App\Tests\Transformer;

use App\Entity\Pet;
use App\Entity\User;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;
use PHPUnit\Framework\TestCase;

class PetTransformerTest extends TestCase
{
    private PetRepository $petRepository;
    private PetTransformer $petTransformer;
    private User $user;

    public function setUp(): void
    {
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->user = $this->createMock(User::class);

        $this->petTransformer = new PetTransformer($this->petRepository);
    }

    /**
     * @test
     * @covers PetTransformer::toEntity
     */
    public function toEntity(): void
    {
        $petId = 1;
        $petDto = (new PetDto())
            ->setName('Test Pet')
            ->setDescription('Test Description')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setCreatedBy(1)
            ->setUpdatedBy(1)
            ->setOwner($this->user);
        $pet = new Pet();

        $this->petRepository->expects($this->once())
            ->method('find')
            ->with($petId)
            ->willReturn($pet);

        $resultPet = $this->petTransformer->toEntity($petId, $petDto, $this->user);

        $this->assertEquals($pet, $resultPet);
    }

    /**
     * @test
     * @covers PetTransformer::toDto
     */
    public function toDto(): void
    {
        $pet = (new Pet())
            ->setName('Test Pet')
            ->setDescription('Test Description')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setCreatedBy(1)
            ->setUpdatedBy(1)
            ->setOwner($this->user);

        $resultDto = $this->petTransformer->toDto($pet);

        $this->assertInstanceOf(PetDto::class, $resultDto);
    }

    /**
     * @test
     * @covers PetTransformer::find
     */
    public function findPet(): void
    {
        $petId = 1;
        $pet = (new Pet())
            ->setName('Test Pet')
            ->setDescription('Test Description')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setCreatedBy(1)
            ->setUpdatedBy(1)
            ->setOwner($this->user);

        $this->petRepository->expects($this->once())
            ->method('find')
            ->with($petId)
            ->willReturn($pet);

        $resultPet = $this->petTransformer->find($petId);

        $this->assertEquals($pet, $resultPet);
    }
}