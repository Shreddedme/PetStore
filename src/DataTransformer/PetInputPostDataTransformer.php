<?php

namespace App\DataTransformer;

use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;

class PetInputPostDataTransformer
{
    public function __construct(
        private PetRepository $petRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function createOrUpdatePet(PetDto $petDto, User $user): PetDto
    {
        if ($petDto->getId()) {
            $pet = $this->find($petDto->getId());

            $pet->setName($petDto->getName());
            $pet->setDescription($petDto->getDescription());
            $pet->setOwner($user);

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            $petDto->setUpdatedAt($pet->getUpdatedAt());
        } else {
            $pet = new Pet();
            $pet->setName($petDto->getName());
            $pet->setDescription($petDto->getDescription());
            $pet->setCreatedBy($user->getId());
            $pet->setOwner($user);

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            $petDto->setId($pet->getId());
            $petDto->setCreatedAt($pet->getCreatedAt());
            $petDto->setUpdatedAt($pet->getUpdatedAt());
            $petDto->setCreatedBy($pet->getCreatedBy());
        }
        $petDto->setOwner($pet->getOwner());

        return $petDto;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(int $id): Pet
    {
        $pet = $this->petRepository->find($id);

        if (!$pet) {
            throw new EntityNotFoundException(Pet::class, $id);
        }

        return $pet;
    }
}