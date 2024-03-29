<?php

namespace App\Tests\Entity;

use App\Entity\OperationHistory;
use App\Entity\Pet;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class OperationHistoryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $operationHistory = new OperationHistory();

        $id = 123;
        $reflection = new \ReflectionClass(OperationHistory::class);
        $property = $reflection->getProperty('id');
        $property->setValue($operationHistory, $id);

        $this->assertSame($id, $operationHistory->getId());

        $user = new User();
        $operationHistory->setPerformedBy($user);
        $this->assertSame($user, $operationHistory->getPerformedBy());

        $pet = new Pet();
        $operationHistory->setPet($pet);
        $this->assertSame($pet, $operationHistory->getPet());

        $date = new \DateTime();
        $operationHistory->setOperationDate($date);
        $this->assertSame($date, $operationHistory->getOperationDate());
    }
}