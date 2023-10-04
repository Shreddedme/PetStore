<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use Doctrine\ORM\EntityManagerInterface;

class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {}

    /**
     * @param UserDto $data
     * @throws EntityNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserDto
    {
        $user = new User();
        $user->setName($data->getName());
        $user->setEmail($data->getEmail());
        $user->setPassword($data->getPassword());
        $user->setRoles($data->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $data->setId($user->getId());
        $data->setName($user->getName());
        $data->setEmail($user->getEmail());
        $data->setRoles($user->getRoles());

        return $data;
    }
}