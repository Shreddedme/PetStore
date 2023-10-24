<?php

namespace App\Model\Dto;

use ApiPlatform\Api\FilterInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="PetRequestDto",
 *     description="Питомец",
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="petDescription", type="string", default="Very lazy"),
 *     @OA\Property(property="owner", type="string", default="John"),
 *     @OA\Property(property="page", type="integer", default=1),
 *     @OA\Property(property="perPage", type="integer", default=10),
 *     @OA\Property(property="sortBy", type="string", default="id"),
 *     @OA\Property(property="sortDirection", type="string", default="asc")
 * )
 */
class PetRequestDto implements FilterInterface
{
    public const PETCOUNT = 10;
    public const SORTBY = 'id';
    public const SORTDIRECTION = 'asc';

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
    private ?string $petDescription = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Page number should be positive'
    )]
    private ?int $page = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Count number should be positive'
    )]
    private int $perPage = self::PETCOUNT;

    private string $sortBy = self::SORTBY;

    private string $sortDirection = self::SORTDIRECTION;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): void
    {
        $this->owner = $owner;
    }

    public function getPetDescription(): ?string
    {
        return $this->petDescription;
    }

    public function setPetDescription(?string $petDescription): void
    {
        $this->petDescription = $petDescription;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(string $sortDirection): void
    {
        $this->sortDirection = $sortDirection;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
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
    }
}