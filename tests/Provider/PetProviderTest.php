<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use App\Entity\Pet;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Service\Provider\PetProvider;
use App\Transformer\PetTransformer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PetProviderTest extends TestCase
{
    private PetRepository $petRepository;
    private PetTransformer $petTransformer;
    private LoggerInterface $logger;
    private PetProvider $petProvider;

    public function setUp(): void
    {
        $this->petRepository = $this->createMock(PetRepository::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->petProvider = new PetProvider($this->petRepository, $this->petTransformer, $this->logger);
    }

    /**
     * @test
     * @covers PetProvider::provide
     */
    public function provide(): void
    {
        $operation = $this->createMock(Operation::class);
        $pet = $this->createMock(Pet::class);
        $petDto = $this->createMock(PetDto::class);
        $uriVariables = ['id' => 1];

        $this->logger->expects($this->exactly(2))
            ->method('info');

        $this->petRepository->expects($this->once())
            ->method('find')
            ->with($uriVariables['id'])
            ->willReturn($pet);

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->with($pet)
            ->willReturn($petDto);

        $result = $this->petProvider->provide($operation, $uriVariables);

        $this->assertEquals($petDto, $result);
    }
}