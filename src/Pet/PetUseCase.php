<?php

namespace App\Pet;

use App\Entity\Pet;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
class PetUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
    )
    {}

    public function create(string $name, string $description): Pet
    {
        $pet = new Pet($name, $description);
        $pet->setCreatedBy(1);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $pet;
    }

    public function find(int $id): ?Pet
    {
        return $this->petRepository->find($id);
    }

    public function findAll(): array
    {
        return $this->petRepository->findAll();
    }

    public function update(
        int $id,
        string $name,
        string $description,
//        int $updatedBy,
    ): Pet
    {

        $pet = $this->find($id);

        if (!$pet) {
            throw new Exception('Pet not found');
        }
        $updatingBy = 2;
        $pet->setName($name);
        $pet->setDescription($description);
        $pet->setUpdatedBy($updatingBy);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $pet;
    }

    public function delete(int $id): void
    {
        $pet = $this->find($id);

        if (!$pet) {
            throw new Exception('Pet not found');
        }

        $this->entityManager->remove($pet);
        $this->entityManager->flush();
    }

}