<?php

namespace App\ApiFilter;

use ApiPlatform\Api\FilterInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

class PetDtoFilter implements FilterInterface
{
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    private ?string $name = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    private ?string $owner = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    private ?string $description = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Page number should be positive'
    )]
    private ?string $page = null;
    public function getDescription(string $resourceClass): array
    {
        return \array_merge(
            [
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
            ],
        );
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): PetDtoFilter
    {
        $this->page = $page;

        return $this;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function setSortBy(?string $sortBy): PetDtoFilter
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function getSortDirection(): ?string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(?string $sortDirection): PetDtoFilter
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): PetDtoFilter
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): PetDtoFilter
    {
        $this->owner = $owner;

        return $this;
    }

}