<?php

namespace App\Tests\ProcessorTest;

use App\Entity\Pet;
use App\Entity\User;
use App\Model\Dto\PetDto;
use App\Processor\PetCreateProcessor;
use App\Transformer\PetTransformer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class PetCreateProcessorTest extends TestCase
{
    private $security;
    private $entityManager;
    private $petTransformer;
    private $user;
    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->petTransformer = $this->createMock(PetTransformer::class);
        $this->user = $this->createMock(User::class);

        $processor = new PetCreateProcessor($this->security, $this->entityManager, $this->petTransformer);

        $petDto = new PetDto();
        $petDto->setName('Cat');
        $petDto->setDescription('Lazy');
        $petDto->setCreatedBy(1);

        $this->petTransformer->expects($this->once())
            ->method('toEntity')
            ->willReturn(new Pet());

        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->petTransformer->expects($this->once())
            ->method('toDto')
            ->willReturn(new PetDto());
    }

    public function test()
    {

    }
}