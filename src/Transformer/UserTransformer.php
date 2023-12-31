<?php

namespace App\Transformer;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;

class UserTransformer
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function toEntity(?int $userId, UserDto $userDto): User
    {
        if ($userId !== null) {
            $user = $this->find($userId);
        } else {
            $user = new User();
        }
        $user
            ->setName($userDto->getName())
            ->setEmail($userDto->getEmail())
            ->setPassword($userDto->getPassword())
            ->setRoles($userDto->getRoles());

        return $user;
    }

    public function toDto(User $user): UserDto
    {
        $userDto = new UserDto();
        $userDto
            ->setId($user->getId())
            ->setName($user->getName())
            ->setEmail($user->getEmail())
            ->setRoles($user->getRoles());

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