<?php

namespace App\Service\Pet;

use App\Entity\Pet;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PetUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
        private UserRepository $userRepository,
    )
    {}

    public function create(PetDto $petDto): Pet
    {
        $user = $this->userRepository->find($petDto->getCreatedBy());
        $pet = new Pet();
        $pet->setName($petDto->getName());
        $pet->setDescription($petDto->getDescription());
        $pet->setCreatedBy($user->getId());
        $pet->setOwner($user);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $pet;
    }

    public function find(int $id): ?Pet
    {
        $pet = $this->petRepository->find($id);

        if (!$pet) {
            throw new Exception('Pet not found');
        }

        return $pet;
    }

    public function findAll(): array
    {
        return $this->petRepository->findAll();
    }

    public function update(int $id, PetDto $petDto): Pet
    {
        $pet = $this->find($id);

        if (!$pet) {
            throw new Exception('Pet not found');
        }
        $authorizedUserId = 1;
        $user = $this->userRepository->find($authorizedUserId);

        if (!$user) {
            throw new Exception('user not found');
        }

        $pet->setName($petDto->getName());
        $pet->setDescription($petDto->getDescription());
        $pet->setUpdatedBy($user->getId());

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