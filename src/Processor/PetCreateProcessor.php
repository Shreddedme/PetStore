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
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PetCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private PetTransformer $petTransformer,
        private UserProviderInterface $userProvider,
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
//        $currentUser = $this->userProvider->loadUserByIdentifier('john');
        $pet = $this->petTransformer->toEntity(null, $data, $currentUser);

        $this->entityManager->persist($pet);
        $this->entityManager->flush();

        return $this->petTransformer->toDto($pet);
    }
}