<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;

class PetProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $pet = $this->petRepository->find($uriVariables['id']);

        $petDto = new PetDto();
        $petDto->setId($pet->getId());
        $petDto->setName($pet->getName());
        $petDto->setDescription($pet->getDescription());
        $petDto->setCreatedAt($pet->getCreatedAt());
        $petDto->setUpdatedAt($pet->getUpdatedAt());
        $petDto->setUpdatedBy($pet->getUpdatedBy());
        $petDto->setCreatedBy($pet->getCreatedBy());
        $petDto->setOwner($pet->getOwner());

        return $petDto;
    }
}