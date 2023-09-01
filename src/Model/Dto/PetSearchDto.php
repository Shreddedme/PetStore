<?php

namespace App\Model\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="PetDto",
 *     description="Питомец",
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="description", type="string", default="Very lazy"),
 *     @OA\Property(property="owner", type="string", default="John"),
 *     @OA\Property(property="page", type="integer", default=1),
 *     @OA\Property(property="count", type="integer", default=10)
 *
 * )
 */
class PetSearchDto
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
        message: 'Paige number should be positive'
    )]
    private ?int $page = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Count number should be positive'
    )]
    private ?int $count = null;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): void
    {
        $this->count = $count;
    }
}