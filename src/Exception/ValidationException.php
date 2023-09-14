<?php

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    private ConstraintViolationListInterface $constraintViolationList;
    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct();
        $this->constraintViolationList = $constraintViolationList;
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        foreach ($this->constraintViolationList as $violation) {
            $errors['field'] = $violation->getPropertyPath();
            $errors['message'] = $violation->getMessage();
        }

        return $errors;
    }
}
