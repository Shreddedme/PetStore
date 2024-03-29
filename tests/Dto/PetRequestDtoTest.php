<?php

namespace App\Tests\Dto;

use App\Model\Dto\PetRequestDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

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

    public function testGetDescription(): void
    {
        $petRequestDto = new PetRequestDto();

        $description = $petRequestDto->getDescription(PetRequestDto::class);

        $expectedDescription = [
            'page' => [
                'property' => 'page',
                'type' => Type::BUILTIN_TYPE_INT,
                'description' => 'Номер страницы',
                'required' => false,
            ],
            'sortBy' => [
                'property' => 'sortBy',
                'type' => Type::BUILTIN_TYPE_STRING,
                'description' => 'Сортировать по',
                'required' => false,
            ],
            'petDescription' => [
                'property' => 'petDescription',
                'type' => Type::BUILTIN_TYPE_STRING,
                'description' => 'Описание питомца',
                'required' => false,
            ],
            'sortDirection' => [
                'property' => 'sortDirection',
                'type' => Type::BUILTIN_TYPE_STRING,
                'description' => 'Направление сортировки',
                'required' => false,
            ],
            'name' => [
                'property' => 'name',
                'type' => Type::BUILTIN_TYPE_STRING,
                'description' => 'Имя питомца',
                'required' => false,
            ],
            'owner' => [
                'property' => 'owner',
                'type' => Type::BUILTIN_TYPE_STRING,
                'description' => 'Имя хозяина',
                'required' => false,
            ],
        ];

        $this->assertSame($expectedDescription, $description);
    }
}