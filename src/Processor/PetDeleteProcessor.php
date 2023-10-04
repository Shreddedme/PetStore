<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;

class PetDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $pet = $this->petRepository->find($uriVariables['id']);

        $this->entityManager->remove($pet);
        $this->entityManager->flush();
    }
}