<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'roles' => $object->getRoles(),
            'createdAt' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof User;
    }
}