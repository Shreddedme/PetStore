<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
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
        $this->logger->info('Attempting to fetch user with id: ' . $uriVariables['id']);

        $user = $this->userRepository->find($uriVariables['id']);

        if (!$user) {
            throw new EntityNotFoundException(User::class, $uriVariables['id']);
        }

        $this->logger->info('User successfully fetched');

        return $this->userTransformer->toDto($user);
    }
}