<?php

namespace App\Tests\Exception;

use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionTest extends TestCase
{
    public function testValidationException(): void
    {
        $violationList = new ConstraintViolationList([
            new ConstraintViolation(
                'Test validation error',
                'Test validation error',
                [],
                '',
                'propertyPath',
                'invalidValue'
            )
        ]);

        $exception = new ValidationException($violationList);

        $this->assertEquals([
            'field' => 'propertyPath',
            'message' => 'Test validation error'
        ], $exception->getValidationErrors());
    }
}