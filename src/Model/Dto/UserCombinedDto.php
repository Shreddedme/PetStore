<?php

namespace App\Model\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="UserCombinedDto",
 *     description="Пользователь",
 *     @OA\Property(property="page", type="integer", default=1),
 * )
 */
class UserCombinedDto
{
    public const USERCOUNT = 2;

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
    private int $count = self::USERCOUNT;

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


}