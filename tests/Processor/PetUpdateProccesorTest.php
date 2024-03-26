<?php

namespace App\Tests\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Service\Processor\PetUpdateProcessor;
use App\Transformer\PetTransformer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class PetUpdateProccesorTest extends TestCase
{
    private User $user;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private PetTransformer $petTransformer;
    private Operation $operation;
    private PetUpdateProcessor $processor;

    public function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->security = $this->createMock(Security::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->operation = $this->createMock(Operation::class);

        $this->processor = new PetUpdateProcessor($this->security, $this->entityManager, $this->petTransformer);
    }

    /**
     * @test
     * @dataProvider petData
     * @covers PetUpdateProcessor::process
     * @param PetDto $petDto
     * @return void
     * @throws EntityNotFoundException
     */
    public function processorProperties(PetDto $petDto): void
    {
        $petId = 1;
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

        $resultDto = $this->processor->process($petDto, $this->operation, ['id' => $petId]);

        $this->assertEquals($petDto, $resultDto);
    }

    public function petData(): array
    {
        return [
            [(new PetDto())->setName('Cat')->setDescription('Lazy')->setCreatedBy(1)],
        ];
    }
}