<?php

namespace App\Model\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="PetCombinedDto",
 *     description="Питомец",
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="description", type="string", default="Very lazy"),
 *     @OA\Property(property="owner", type="string", default="John"),
 *     @OA\Property(property="page", type="integer", default=1),
 *     @OA\Property(property="count", type="integer", default=10),
 *     @OA\Property(property="sortBy", type="string", default="id"),
 *     @OA\Property(property="sortDirection", type="string", default="asc")
 * )
 */
class PetCombinedDto
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
    private ?string $description = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Page number should be positive'
    )]
    private ?string $page = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Count number should be positive'
    )]
    private int $count = self::PETCOUNT;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
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
}