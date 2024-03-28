<?php

namespace App\Tests\Exception;

use App\Exception\EntityNotFoundException;
use PHPUnit\Framework\TestCase;

class EntityNotFoundExceptionTest extends TestCase
{
    public function testEntityNotFoundException(): void
    {
        $entityName = 'TestEntity';
        $id = 1;

        try {
            throw new EntityNotFoundException($entityName, $id);
        } catch (EntityNotFoundException $e) {
            $this->assertEquals("Entity 'TestEntity' with ID '1' not found.", $e->getMessage());
        }
    }
}