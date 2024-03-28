<?php

namespace App\Tests\Dto;

use App\Model\Dto\UserRequestDto;
use PHPUnit\Framework\TestCase;

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
}