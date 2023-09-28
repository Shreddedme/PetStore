<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\DataTransformer\PetInputPostDataTransformer;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use Symfony\Bundle\SecurityBundle\Security;

class PetUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private ValidatorInterface $validator,
        private PetInputPostDataTransformer $inputPostDataTransformer,
    )
    {}

    /**
     * @param PetDto $data
     * @throws EntityNotFoundException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): PetDto
    {
        $this->validator->validate($data);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        return $this->inputPostDataTransformer->createOrUpdatePet($data, $currentUser);
    }
}