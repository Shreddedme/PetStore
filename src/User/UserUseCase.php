<?php

namespace App\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
class UserUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    )
    {}

    public function create(string $name, string $roles): ?User
    {
        $user = new User($name, $roles);
        $user->setCreatedBy(1);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function find(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function update(
        int $id,
        string $name,
        string $roles,
//        int $updatetBy
    ): User
    {
        $user = $this->find($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        $updatingBy = 2;

        $user->setName($name);
        $user->setRoles($roles);
        $user->setUpdatedBy($updatingBy);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->find($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}