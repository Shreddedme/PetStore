<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->userRepository->find($uriVariables['id']);

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}