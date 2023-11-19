<?php

namespace App\Service\Transactions;

use App\Exception\EntityNotFoundException;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PessimisticLockException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:update-pets'
)]
class PetUpdateTransaction extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
    )
    {
        parent::__construct();
    }

    /**
     * @throws Throwable
     * @throws PessimisticLockException
     * @throws EntityNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->entityManager->beginTransaction();

            $pets = $this->petRepository->findAll();

            foreach ($pets as $pet) {
                $pet->setName(($pet->getName(). '_1'));
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            $output->writeln('Pets updated successfully');
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            throw $e;
        }

        return Command::SUCCESS;
    }
}