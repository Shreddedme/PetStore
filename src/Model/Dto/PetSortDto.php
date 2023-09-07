<?php

namespace App\Model\Dto;

class PetSortDto
{
    private string $sortBy = 'id';
    private string $sortDirection = 'asc';

    /**
     * @return string
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return mixed
     */
    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    /**
     * @param mixed $sortDirection
     */
    public function setSortDirection(string $sortDirection): void
    {
        $this->sortDirection = $sortDirection;
    }

}