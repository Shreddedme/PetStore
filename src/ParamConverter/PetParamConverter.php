<?php

namespace App\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\PetDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetParamConverter implements ParamConverterInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    )
    {}

    public function apply(Request $request, ParamConverter $configuration)
    {
        $jsonContent = $request->getContent();
        $petDto = $this->serializer->deserialize($jsonContent, PetDto::class, 'json');

        $errors = $this->validator->validate($petDto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $request->attributes->set($configuration->getName(), $petDto);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === PetDto::class;
    }
}