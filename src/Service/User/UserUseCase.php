<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserCombinedDto;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserTransformer $userTransformer,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function create(UserDto $userDto): UserDto
    {
        $user = $this->userTransformer->toEntity(null, $userDto);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->userTransformer->toDto($user);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(int $id): User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new EntityNotFoundException(User::class, $id);
        }

        return $user;
    }

    public function getOne(User $user): UserDto
    {
       return $this->userTransformer->toDto($user);
    }

    public function getAllUsers(UserCombinedDto $userCombinedDto): Paginator
    {
        return $this->userRepository->getAllUsers($userCombinedDto);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(int $id, UserDto $userDto): UserDto
    {
        $user = $this->userTransformer->toEntity($id, $userDto);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->userTransformer->toDto($user);
    }

    public function delete(int $id): void
    {
        try {
            $user = $this->find($id);
        } catch (EntityNotFoundException $e) {
        }

        if (isset($user) && $user instanceof User) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }
}