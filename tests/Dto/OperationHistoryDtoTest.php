<?php

namespace App\Tests\Dto;

use App\Entity\Pet;
use App\Entity\User;
use App\Model\Dto\OperationHistoryDto;
use PHPUnit\Framework\TestCase;

class OperationHistoryDtoTest extends TestCase
{
    public function testOperationHistoryDto(): void
    {
        $user = new User();
        $pet = new Pet();
        $date = new \DateTime();

        $operationHistoryDto = new OperationHistoryDto();
        $operationHistoryDto->setPerformedBy($user);
        $operationHistoryDto->setPet($pet);
        $operationHistoryDto->setOperationDate($date);

        $this->assertSame($user, $operationHistoryDto->getPerformedBy());
        $this->assertSame($pet, $operationHistoryDto->getPet());
        $this->assertSame($date, $operationHistoryDto->getOperationDate());
    }
}