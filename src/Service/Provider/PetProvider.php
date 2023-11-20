<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exception\EntityNotFoundException;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;
use Psr\Log\LoggerInterface;

class PetProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private PetTransformer $petTransformer,
        private LoggerInterface $logger,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
            $petId = $uriVariables['id'] ?? null;

            $this->logger->info('Attempting to fetch pet with id: ' . $petId);

            $pet = $this->petRepository->find($petId);

            if (!$pet) {
                throw new EntityNotFoundException('Pet not found', $petId);
            }

            $this->logger->info('Pet successfully fetched');

            return $this->petTransformer->toDto($pet);
    }
}