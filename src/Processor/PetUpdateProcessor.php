<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Pet;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PetUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private PetRepository $petRepository,
    )
    {}

    /**
     * @param PetDto $data
     * @throws EntityNotFoundException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): PetDto
    {
        $petId = $uriVariables['id'];

        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        $pet = $this->find($petId);
        $pet->setName($data->getName());
        $pet->setDescription($data->getDescription());

        if ($currentUser !== $pet->getOwner()) {
            $pet->setUpdatedBy($currentUser->getId());
            $pet->setOwner($currentUser);
        }

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        $data->setId($pet->getId());
        $data->setCreatedAt($pet->getCreatedAt());
        $data->setUpdatedBy($currentUser->getId());
        $data->setUpdatedAt($pet->getUpdatedAt());
        $data->setCreatedBy($pet->getCreatedBy());
        $data->setOwner($pet->getOwner());

        return $data;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(int $id): Pet
    {
        $pet = $this->petRepository->find($id);

        if (!$pet) {
            throw new EntityNotFoundException(Pet::class, $id);
        }

        return $pet;
    }
}