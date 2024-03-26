<?php

namespace App\Tests\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Service\Processor\PetDeleteProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PetDeleteProcessorTest extends TestCase
{
    private PetRepository $petRepository;
    private EntityManagerInterface $entityManager;
    private Operation $operation;
    private PetDeleteProcessor $processor;

    public function setUp(): void
    {
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->operation = $this->createMock(Operation::class);

        $this->processor = new PetDeleteProcessor($this->petRepository, $this->entityManager);
    }

    /**
     * @test
     * @covers PetDeleteProcessor::process
     */
    public function processorProperties(): void
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

        $this->processor->process(null, $this->operation, ['id' => $petId]);
    }
}