<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;

class UserUpdateProccesor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserTransformer $userTransformer,
    )
    {}

    /**
     * @param UserDto $data
     * @throws EntityNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserDto
    {
        $userId = $uriVariables['id'];
        $user = $this->userTransformer->toEntity($userId, $data);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->userTransformer->toDto($user);
    }


}