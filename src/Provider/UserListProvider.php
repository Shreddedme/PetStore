<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exception\ValidationException;
use App\Model\Dto\UserCombinedDto;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserListProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserTransformer $userTransformer,
    )
    {}

    /**
     * @throws ValidationException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $context['filters'] ?? null;
        $userCombinedDto = $this->serializer->denormalize($parameters, UserCombinedDto::class, null, [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);

        $errors = $this->validator->validate($userCombinedDto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
       $users = $this->userRepository->getAllUsers($userCombinedDto);

       $userDtos = [];

       foreach ($users as $user) {
           $userDtos[] = $this->userTransformer->toDto($user);
       }

       return $userDtos;
    }
}