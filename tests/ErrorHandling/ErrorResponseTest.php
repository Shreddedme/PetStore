<?php

namespace App\Tests\ErrorHandling;

use App\Model\ErrorHandling\ErrorResponse;
use PHPUnit\Framework\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testErrorResponse(): void
    {
        $errorResponse = new ErrorResponse();

        $error = ['field' => 'name', 'message' => 'Forbidden characters cant be entered'];
        $errorResponse->addError($error);

        $this->assertSame([$error], $errorResponse->getErrors());
    }
}