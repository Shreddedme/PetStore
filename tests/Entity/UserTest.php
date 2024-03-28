<?php

namespace App\Tests\Entity;

use App\Entity\OperationHistory;
use App\Entity\Pet;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $name = 'Test User';
        $user->setName($name);
        $this->assertSame($name, $user->getName());

        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());

        $password = 'password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());

        $roles = ['ROLE_USER'];
        $user->setRoles($roles);
        $this->assertSame($roles, $user->getRoles());

        $createdBy = 1;
        $user->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $user->getCreatedBy());

        $updatedBy = 2;
        $user->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $user->getUpdatedBy());

        $pet = new Pet();
        $user->addPet($pet);
        $this->assertSame($pet, $user->getPets()->first());

        $operationHistory = new OperationHistory();
        $user->addOperationHistory($operationHistory);
    }
}