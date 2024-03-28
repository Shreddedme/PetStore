<?php

namespace App\Tests\Dto;

use App\Model\Dto\PetRequestDto;
use PHPUnit\Framework\TestCase;

class PetRequestDtoTest extends TestCase
{
    public function testPetRequestDto(): void
    {
        $petRequestDto = new PetRequestDto();

        $petRequestDto->setName('Test Pet');
        $petRequestDto->setOwner('Test Owner');
        $petRequestDto->setPetDescription('Test Description');
        $petRequestDto->setPage(1);
        $petRequestDto->setPerPage(10);
        $petRequestDto->setSortBy('id');
        $petRequestDto->setSortDirection('asc');

        $this->assertSame('Test Pet', $petRequestDto->getName());
        $this->assertSame('Test Owner', $petRequestDto->getOwner());
        $this->assertSame('Test Description', $petRequestDto->getPetDescription());
        $this->assertSame(1, $petRequestDto->getPage());
        $this->assertSame(10, $petRequestDto->getPerPage());
        $this->assertSame('id', $petRequestDto->getSortBy());
        $this->assertSame('asc', $petRequestDto->getSortDirection());
    }
}