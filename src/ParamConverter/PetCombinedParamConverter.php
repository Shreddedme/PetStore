<?php

namespace App\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\PetSearchDto;
use App\Model\Dto\PetSortDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetCombinedParamConverter implements ParamConverterInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    )
    {}

    /**
     * @throws ValidationException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $petDto = null;

        if ($configuration->getClass() === PetSortDto::class) {
            try {
                $parameters = $request->query->all();
                $petDto = $this->serializer->denormalize($parameters, PetSortDto::class);
            } catch (NotEncodableValueException $e) {
                throw new BadRequestHttpException('Invalid format', $e);
            }
        } elseif ($configuration->getClass() === PetSearchDto::class) {
            try {
                $jsonContent = $request->getContent();
                $petDto = $this->serializer->deserialize($jsonContent, PetSearchDto::class, 'json');
            } catch (NotEncodableValueException $e) {
                throw new BadRequestHttpException('Invalid format', $e);
            }
        }

        $errors = $this->validator->validate($petDto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $request->attributes->set($configuration->getName(), $petDto);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return in_array($configuration->getClass(), [PetSortDto::class, PetSearchDto::class]);
    }
}