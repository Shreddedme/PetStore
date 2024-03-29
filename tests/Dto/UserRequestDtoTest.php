<?php

namespace App\Tests\Dto;

use App\Model\Dto\UserRequestDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

class UserRequestDtoTest extends TestCase
{
    public function testUserRequestDto(): void
    {
        $userRequestDto = new UserRequestDto();

        $userRequestDto->setPage(1);
        $userRequestDto->setCount(2);

        $this->assertSame(1, $userRequestDto->getPage());
        $this->assertSame(2, $userRequestDto->getCount());
    }

    public function testGetDescription(): void
    {
        $userRequestDto = new UserRequestDto();

        $description = $userRequestDto->getDescription(UserRequestDto::class);

        $expectedDescription = [
            'page' => [
                'property' => 'page',
                'type' => Type::BUILTIN_TYPE_INT,
                'description' => 'Номер страницы',
                'required' => false,
            ],
        ];

        $this->assertSame($expectedDescription, $description);
    }
}