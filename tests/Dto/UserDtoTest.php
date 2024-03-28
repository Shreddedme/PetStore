<?php

namespace App\Tests\Dto;

use App\Model\Dto\UserDto;
use PHPUnit\Framework\TestCase;

class UserDtoTest extends TestCase
{
    public function testUserDto(): void
    {
        $userDto = new UserDto();

        $userDto->setId(1);
        $userDto->setName('Test User');
        $userDto->setPassword('password');
        $userDto->setEmail('test@example.com');
        $userDto->setRoles(['ROLE_USER']);

        $this->assertSame(1, $userDto->getId());
        $this->assertSame('Test User', $userDto->getName());
        $this->assertSame('password', $userDto->getPassword());
        $this->assertSame('test@example.com', $userDto->getEmail());
        $this->assertSame(['ROLE_USER'], $userDto->getRoles());
    }
}