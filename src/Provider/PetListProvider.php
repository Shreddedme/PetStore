<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exception\ValidationException;
use App\Model\Dto\PetCombinedDto;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetListProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    )
    {}

    /**
     * @throws ValidationException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $context['filters'] ?? null;
        $petCombinedDto = $this->serializer->denormalize($parameters, PetCombinedDto::class, null, [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);

        $errors = $this->validator->validate($petCombinedDto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $pets = $this->petRepository->findByFilter($petCombinedDto);
        $petDtos = [];

        foreach ($pets as $pet) {
            $petDto = new PetDto();
            $petDto->setId($pet->getId());
            $petDto->setName($pet->getName());
            $petDto->setDescription($pet->getDescription());
            $petDto->setCreatedAt($pet->getCreatedAt());
            $petDto->setUpdatedAt($pet->getUpdatedAt());
            $petDto->setUpdatedBy($pet->getUpdatedBy());
            $petDto->setOwner($pet->getOwner());
            $petDtos[] = $petDto;
        }
        return $petDtos;
    }
}