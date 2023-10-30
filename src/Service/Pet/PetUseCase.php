<?php

namespace App\Service\Pet;

use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetRequestDto;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\SecurityBundle\Security;

class PetUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
        private Security $security,
        private PetTransformer $petTransformer,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function create(PetDto $petDto): PetDto
    {
        $currentUser = $this->security->getUser();

        if ($currentUser instanceof User) {
            $pet = $this->petTransformer->toEntity(null, $petDto, $currentUser);

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            return $this->petTransformer->toDto($pet);
        }

        throw new \LogicException('Current user is not authenticated');
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

    public function getOne(Pet $pet): PetDto
    {
        return $this->petTransformer->toDto($pet);
    }

    public function findByFilter(PetRequestDto $petRequestDto): Paginator
    {
        return $this->petRepository->findByFilter($petRequestDto);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(int $id, PetDto $petDto): PetDto
    {
        $currentUser = $this->security->getUser();

        if ($currentUser instanceof User) {
           $pet = $this->petTransformer->toEntity($id, $petDto, $currentUser);

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            return $this->petTransformer->toDto($pet);
        }

        throw new \LogicException('Current user is not authenticated');
    }

    public function delete(int $id): void
    {
        try {
            $pet = $this->find($id);
        } catch (EntityNotFoundException $e) {
        }

        if (isset($pet) && $pet instanceof Pet) {
            $this->entityManager->remove($pet);
            $this->entityManager->flush();
        }
    }
}