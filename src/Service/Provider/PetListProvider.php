<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Model\Dto\PetRequestDto;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PetListProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private PetTransformer $petTransformer,
        private LoggerInterface $logger,
    )
    {}

    /**
     * @throws \JsonException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $context['filters'] ?? null;
        $petCombinedDto = $this->serializer->denormalize(
            $parameters,
            PetRequestDto::class,
            null,
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
        );

        $this->logger->debug(null, $context);

        $this->validator->validate($petCombinedDto);

        $pets = $this->petRepository->findByFilter($petCombinedDto);
        $petDtos = [];

        foreach ($pets as $pet) {
            $petDtos[] = $this->petTransformer->toDto($pet);
        }

        return $petDtos;
    }
}