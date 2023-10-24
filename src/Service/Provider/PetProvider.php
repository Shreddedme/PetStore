<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;

class PetProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private PetTransformer $petTransformer,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $pet = $this->petRepository->find($uriVariables['id']);

        return $this->petTransformer->toDto($pet);
    }
}