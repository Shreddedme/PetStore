<?php

namespace App\Tests\Dto;

use App\Entity\User;
use App\Model\Dto\PetDto;
use PHPUnit\Framework\TestCase;

class PetDtoTest extends TestCase
{
    public function testPetDto(): void
    {
        $user = new User();
        $petDto = new PetDto();

        $petDto->setName('Test Pet');
        $petDto->setDescription('Test Description');
        $petDto->setCreatedBy(1);
        $petDto->setOwner($user);

        $this->assertSame('Test Pet', $petDto->getName());
        $this->assertSame('Test Description', $petDto->getDescription());
        $this->assertSame(1, $petDto->getCreatedBy());
        $this->assertSame($user, $petDto->getOwner());
    }
}