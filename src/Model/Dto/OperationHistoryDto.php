<?php

namespace App\Model\Dto;

use App\Entity\Pet;
use App\Entity\User;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

class OperationHistoryDto
{
    const OPERATION_HISTORY_GET = 'get';

    #[Groups(self::OPERATION_HISTORY_GET)]
    private DateTime $operationDate;

    #[Groups(self::OPERATION_HISTORY_GET)]
    private User $performedBy;

    #[Groups(self::OPERATION_HISTORY_GET)]
    private ?Pet $pet = null;

    public function getOperationDate(): DateTime
    {
        return $this->operationDate;
    }

    public function setOperationDate(DateTime $operationDate): self
    {
        $this->operationDate = $operationDate;

        return $this;
    }

    public function getPerformedBy(): User
    {
        return $this->performedBy;
    }

    public function setPerformedBy(User $performedBy): self
    {
        $this->performedBy = $performedBy;

        return $this;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(?Pet $pet): self
    {
        $this->pet = $pet;

        return $this;
    }
}