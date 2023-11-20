<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exception\EntityNotFoundException;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Psr\Log\LoggerInterface;

class UserProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserTransformer $userTransformer,
        private LoggerInterface $logger,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $this->userRepository->find($uriVariables['id']);

        $this->logger->info('Attempting to fetch user with id: ' . $userId);

        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new EntityNotFoundException('User not found', $userId);
        }

        $this->logger->info('User successfully fetched');

        return $this->userTransformer->toDto($user);
    }
}