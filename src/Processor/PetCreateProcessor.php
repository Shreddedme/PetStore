<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\DataTransformer\PetInputPostDataTransformer;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class PetCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserRepository $userRepository,
        private Security $security,
        private PetInputPostDataTransformer $inputPostDataTransformer,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): PetDto
    {
        $this->validator->validate($data);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        $user = $this->userRepository->find($currentUser->getId());

        return $this->inputPostDataTransformer->createOrUpdatePet($data, $user);
    }
}