<?php

namespace App\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\UserCombinedDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCombinedParamConverter implements ParamConverterInterface
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
        $parameters = $request->query->all();

        try {
            $userCombinedDto = $this->serializer->denormalize($parameters, UserCombinedDto::class, null, [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
        } catch (NotEncodableValueException $e) {
            throw new BadRequestHttpException('Invalid format', $e);
        }

        $errors = $this->validator->validate($userCombinedDto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $request->attributes->set($configuration->getName(), $userCombinedDto);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === UserCombinedDto::class;
    }
}