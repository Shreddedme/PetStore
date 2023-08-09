<?php

namespace App\Model\Validator;

use App\Model\Dto\PetDto;
use Exception;

class PetValidator
{
    public function validate(PetDto $petDto): bool
    {
        if (!$petDto->getName() || mb_strlen($petDto->getName()) > 255) {
                throw new Exception('set the field "name" to 255 characters long');
        }
        if (!$petDto->getDescription() || mb_strlen($petDto->getDescription()) > 255) {
            throw new Exception('set the field "description" to 255 characters long');
        }
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $petDto->getName())) {
            throw new Exception('The field "name" should not contain special characters');
        }
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $petDto->getDescription())) {
            throw new Exception('The field "description" should not contain special characters');
        }

        return true;
    }
}