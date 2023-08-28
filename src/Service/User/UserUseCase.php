<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
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

    public function create(UserDto $userDto): User
    {
        $user = new User();
        $user->setName($userDto->getName());
        $user->setRoles($userDto->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function find(int $id): ?User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new EntityNotFoundException(User::class, $id);
        }

        return $user;
    }

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function update(int $id, UserDto $userDto): User
    {
        $user = $this->find($id);

        $user->setName($userDto->getName());
        $user->setRoles($userDto->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function delete(int $id): void
    {
        try {
            $user = $this->find($id);

            if ($user) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            }
        } catch (EntityNotFoundException $e) {
        }
    }
}