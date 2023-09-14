<?php

namespace App\Service\Pet;

use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetCombinedDto;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PetUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
        private UserRepository $userRepository,
        private Security $security,
    )
    {}

    public function create(PetDto $petDto): Pet
    {
        $currentUser = $this->security->getUser();

        if ($currentUser instanceof User) {
            $user = $this->userRepository->find($currentUser->getId());

            $pet = new Pet();
            $pet->setName($petDto->getName());
            $pet->setDescription($petDto->getDescription());
            $pet->setCreatedBy($user->getId());
            $pet->setOwner($user);

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            return $pet;
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


    public function findByFilter(PetCombinedDto $petCombinedDto): Paginator
    {
        return $this->petRepository->findByFilter($petCombinedDto);
    }


    /**
     * @throws EntityNotFoundException
     */
    public function update(int $id, PetDto $petDto): Pet
    {
        $pet = $this->find($id);

        $currentUser = $this->security->getUser();

        if ($currentUser instanceof User) {
            $userId = $this->userRepository->find($currentUser->getId());

            $user = $this->userRepository->find($userId);

            if (!$user) {
                throw new EntityNotFoundException(User::class, $id);
            }

            $pet->setName($petDto->getName());
            $pet->setDescription($petDto->getDescription());
            $pet->setUpdatedBy($user->getId());

            $this->entityManager->persist($pet);
            $this->entityManager->flush();

            return $pet;
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