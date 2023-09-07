<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserUseCase
{
    public const EXPIREDTIME = 3600;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    )
    {}

    public function create(UserDto $userDto): User
    {
        $user = new User();
        $user->setName($userDto->getName());
        $user->setEmail($userDto->getEmail());
        $user->setPassword($userDto->getPassword());
        $user->setRoles($userDto->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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

    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @throws EntityNotFoundException
     */
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
        } catch (EntityNotFoundException $e) {
        }

        if (isset($user) && $user instanceof User) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }
}