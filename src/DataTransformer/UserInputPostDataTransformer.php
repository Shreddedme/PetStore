<?php

namespace App\DataTransformer;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserInputPostDataTransformer
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function createOrUpdatePet(UserDto $userDto): UserDto
    {
        if ($userDto->getId()) {
            $user = $this->find($userDto->getId());

            $user->setName($userDto->getName());
            $user->setEmail($userDto->getEmail());

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            $user = new User();
            $user->setName($userDto->getName());
            $user->setEmail($userDto->getEmail());
            $user->setPassword($userDto->getPassword());
            $user->setRoles($userDto->getRoles());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userDto->setId($user->getId());
            $userDto->setName($user->getName());
        }

        return $userDto;
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
}