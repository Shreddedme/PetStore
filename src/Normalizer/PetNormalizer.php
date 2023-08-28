<?php

namespace App\Normalizer;

use App\Entity\Pet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PetNormalizer implements  NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'createdAt' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $object->getUpdatedAt()->format('Y-m-d H:i:s'),
            'createdBy' => $object->getcreatedBy(),
            'updatedBy' => $object->getUpdatedBy(),
            'owner' => [
                'name' => $object->getOwner()->getName(),
            ],
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Pet;
    }
}