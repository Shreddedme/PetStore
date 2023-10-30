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
            $pet
                ->setCreatedBy($user->getId())
                ->setOwner($user);
        }
        $pet
            ->setName($petDto->getName())
            ->setDescription($petDto->getDescription());

        return $pet;
    }

    public function toDto(Pet $pet): PetDto
    {
        $petDto = new PetDto();
        $petDto
            ->setId($pet->getId())
            ->setName($pet->getName())
            ->setDescription($pet->getDescription())
            ->setCreatedAt($pet->getCreatedAt())
            ->setUpdatedAt($pet->getUpdatedAt())
            ->setCreatedBy($pet->getCreatedBy())
            ->setUpdatedBy($pet->getUpdatedBy())
            ->setOwner($pet->getOwner());

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