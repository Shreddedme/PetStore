<?php

namespace App\Tests\Transaction;

use App\Entity\Pet;
use App\Repository\PetRepository;
use App\Service\Transactions\PetUpdateTransaction;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PetUpdateTransactionTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PetRepository $petRepository;
    private PetUpdateTransaction $petUpdateTransaction;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->petRepository = $this->createMock(PetRepository::class);

        $this->petUpdateTransaction = new PetUpdateTransaction($this->entityManager, $this->petRepository);
    }

    /**
     * @test
     * @covers PetUpdateTransaction::execute
     */
    public function execute(): void
    {
        $pet = $this->createMock(Pet::class);
        $pet->expects($this->once())
            ->method('setName');

        $this->petRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$pet]);

        $this->entityManager->expects($this->once())
            ->method('beginTransaction');
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->entityManager->expects($this->once())
            ->method('commit');

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $output->expects($this->once())
            ->method('writeln')
            ->with('Pets updated successfully');

        $result = $this->petUpdateTransaction->execute($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}