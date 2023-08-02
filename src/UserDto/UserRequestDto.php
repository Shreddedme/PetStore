<?php

namespace App\UserDto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;


class UserRequestDto
{
    public readonly string $name;
    public readonly string $roles;
    public function __construct(
          string $name = 'john',
          string $roles = 'admin',
    )
    {
        $this->name = $name;
        $this->roles = $roles;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRoles(): string
    {
        return $this->roles;
    }

    public function setRoles(string $roles): void
    {
        $this->roles = $roles;
    }

}