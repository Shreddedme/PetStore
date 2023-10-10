<?php

namespace App\ApiFilter;

use ApiPlatform\Api\FilterInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Validator\Constraints as Assert;

class UserDtoFilter implements FilterInterface
{
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Positive(
        message: 'Page number should be positive'
    )]
    private ?int $page = null;
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
            ],
        );
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): UserDtoFilter
    {
        $this->page = $page;

        return $this;
    }

}