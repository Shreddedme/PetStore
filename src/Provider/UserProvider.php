<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Model\Dto\UserDto;
use App\Repository\UserRepository;

class UserProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->userRepository->find($uriVariables['id']);

        $userDto = new UserDto();
        $userDto->setId($user->getId());
        $userDto->setName($user->getName());
        $userDto->setEmail($user->getEmail());
        $userDto->setRoles($user->getRoles());

        return $userDto;
    }
}