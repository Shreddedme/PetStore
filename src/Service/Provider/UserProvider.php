<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;

class UserProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserTransformer $userTransformer,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->userRepository->find($uriVariables['id']);

        return $this->userTransformer->toDto($user);
    }
}