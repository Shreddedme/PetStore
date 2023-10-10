<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Transformer\PetTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PetCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private PetTransformer $petTransformer,
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
        $pet = $this->petTransformer->toEntity(null, $data, $currentUser);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $this->petTransformer->toDto($data, $pet);
    }
}