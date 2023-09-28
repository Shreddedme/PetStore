<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\DataTransformer\UserInputPostDataTransformer;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;

class UserCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserInputPostDataTransformer $inputPostDataTransformer,
    )
    {}

    /**
     * @throws EntityNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserDto
    {
        $this->validator->validate($data);

        return $this->inputPostDataTransformer->createOrUpdatePet($data);
    }
}