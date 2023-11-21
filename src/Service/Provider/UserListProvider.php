<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Exception\ValidationException;
use App\Model\Dto\UserRequestDto;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserListProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private SerializerInterface $serializer,
        private UserTransformer $userTransformer,
        private LoggerInterface $logger,
        private ValidatorInterface $validator,
    )
    {}

    /**
     * @throws ValidationException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $context['filters'] ?? null;
        $userCombinedDto = $this->serializer->denormalize(
            $parameters,
            UserRequestDto::class,
            null,
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
        );

        $this->logger->debug('Error in user list request', $context);

        $this->validator->validate($userCombinedDto);

        $users = $this->userRepository->getAllUsers($userCombinedDto);

       $userDtos = [];

       foreach ($users as $user) {
           $userDtos[] = $this->userTransformer->toDto($user);
       }

       return $userDtos;
    }
}