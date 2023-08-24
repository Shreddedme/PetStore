<?php
declare(strict_types=1);

namespace App\Model\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="PetDto",
 *     description="Питомец",
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="description", type="string", default="Very lazy"),
 *     @OA\Property(property="createdBy", type="integer", default=1)
 * )
 */
class PetDto
{
    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    private string $name;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    private string $description;

    private int $createdBy;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}