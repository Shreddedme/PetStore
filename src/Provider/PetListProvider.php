<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exception\ValidationException;
use App\Model\Dto\PetCombinedDto;
use App\Repository\PetRepository;
use App\Transformer\PetTransformer;
use PHPUnit\Framework\Exception;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetListProvider implements ProviderInterface
{
    public function __construct(
        private PetRepository $petRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private PetTransformer $petTransformer,
    )
    {}

    /**
     * @throws ValidationException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $context['filters'] ?? null;
        $petCombinedDto = $this->serializer->denormalize(
            $parameters,
            PetCombinedDto::class,
            null,
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
        );

        $errors = $this->validator->validate($petCombinedDto);

        if (count($errors) > 0) {
            throw new Exception('syntax error');
//            throw new ValidationException($errors);
        }

        $pets = $this->petRepository->findByFilter($petCombinedDto);
        $petDtos = [];

        foreach ($pets as $pet) {
            $petDtos[] = $this->petTransformer->toDto($pet);
        }

        return $petDtos;
    }
}