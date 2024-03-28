<?php

namespace App\Tests\Entity;

use App\Entity\OperationHistory;
use App\Entity\Pet;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PetTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $pet = new Pet();

        $user = new User();
        $pet->setOwner($user);
        $this->assertSame($user, $pet->getOwner());

        $name = 'Test Pet';
        $pet->setName($name);
        $this->assertSame($name, $pet->getName());

        $description = 'Test Description';
        $pet->setDescription($description);
        $this->assertSame($description, $pet->getDescription());

        $createdBy = 1;
        $pet->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $pet->getCreatedBy());

        $updatedBy = 2;
        $pet->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $pet->getUpdatedBy());

        $operationHistory = new OperationHistory();
        $pet->addOperationHistory($operationHistory);
        $this->assertSame($operationHistory, $pet->getOperationHistory()->first());
    }
}