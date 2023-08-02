<?php

namespace App\UserDto;

class UserResponseDto
{
    public function __construct(
        private string $name,
        private string $roles,
        private \DateTime $createdAt,
        private \DateTime $updatedAt,
        private int $createdBy,
        private int $updatedBy,
    )
    {}

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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getUpdatedBy(): int
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(int $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}