<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PetCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security,
        private EntityManagerInterface $entityManager,
    )
    {}

    /**
     * @param PetDto $data
     * @throws EntityNotFoundException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): PetDto
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        $user = $this->userRepository->find($currentUser->getId());

        $pet = new Pet();
        $pet->setName($data->getName());
        $pet->setDescription($data->getDescription());
        $pet->setCreatedBy($user->getId());
        $pet->setUpdatedBy($data->getUpdatedBy());
        $pet->setOwner($user);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        $data->setId($pet->getId());
        $data->setCreatedAt($pet->getCreatedAt());
        $data->setUpdatedAt($pet->getUpdatedAt());
        $data->setCreatedBy($pet->getCreatedBy());
        $data->setOwner($pet->getOwner());

        return $data;
    }
}