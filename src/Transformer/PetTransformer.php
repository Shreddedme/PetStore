<?php

namespace App\Transformer;

use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;

class PetTransformer
{
    public function __construct(
        private PetRepository $petRepository,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function toEntity(?int $petId, PetDto $petDto, User $user): Pet
    {
        if ($petId !== null) {
            $pet = $this->find($petId);
            $pet->setUpdatedBy($user->getId());
        } else {
            $pet = new Pet();
        }
        $pet->setName($petDto->getName());
        $pet->setDescription($petDto->getDescription());
        $pet->setCreatedBy($user->getId());
        $pet->setOwner($user);

        return $pet;
    }

    public function toDto(PetDto $petDto, Pet $pet): PetDto
    {
        $petDto->setId($pet->getId());
        $petDto->setCreatedAt($pet->getCreatedAt());
        $petDto->setUpdatedAt($pet->getUpdatedAt());
        $petDto->setCreatedBy($pet->getCreatedBy());
        $petDto->setUpdatedBy($pet->getUpdatedBy());
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