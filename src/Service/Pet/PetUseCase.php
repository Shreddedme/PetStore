<?php

namespace App\Service\Pet;

use App\Entity\Pet;
use App\Entity\User;
use App\Model\Dto\PetDto;
use App\Model\Validator\PetValidator;
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
        private PetValidator $petValidator,
    )
    {}

    public function create(PetDto $petDto): Pet
    {
        $user = $this->userRepository->find($petDto->getCreatedBy());
        $this->petValidator->validate($petDto);
        $pet = new Pet();
        $pet->setName($petDto->getName());
        $pet->setDescription($petDto->getDescription());
        $pet->setCreatedBy($user->getId());
        $pet->setOwnerName($user->getName());

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

//    public function update(
//        int $id,
//        string $name,
//        string $description,
//        int $userId,
//    ): Pet
//    {
//
//        $pet = $this->find($id);
//
//        if (!$pet) {
//            throw new Exception('Pet not found');
//        }
//
//        $user = $this->userRepository->find($userId);
//
//        if (!$user) {
//            throw new Exception('user not found');
//        }
//
//        $pet->setName($name);
//        $pet->setDescription($description);
//        $pet->setUpdatedBy($user->getId());
//        $pet->setOwnerName($user->getName());
//
//        $this->entityManager->persist($pet);
//        $this->entityManager->flush();
//
//        return $pet;
//    }
    public function update(int $id, PetDto $petDto): Pet
    {

        $pet = $this->find($id);

        if (!$pet) {
            throw new Exception('Pet not found');
        }

        $user = $this->userRepository->find($petDto->getCreatedBy());

        if (!$user) {
            throw new Exception('user not found');
        }

        $pet->setName($petDto->getName());
        $pet->setDescription($petDto->getDescription());
        $pet->setUpdatedBy($user->getId());
        $pet->setOwnerName($user->getName());

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