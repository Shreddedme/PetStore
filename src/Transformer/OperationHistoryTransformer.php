<?php

namespace App\Transformer;

use App\Entity\OperationHistory;
use App\Model\Dto\OperationHistoryDto;

class OperationHistoryTransformer
{
    public function toDto(OperationHistory $operationHistory): OperationHistoryDto
    {
        $operationHistoryDto = new OperationHistoryDto();
        $operationHistoryDto
            ->setOperationDate($operationHistory->getOperationDate())
            ->setPerformedBy($operationHistory->getPerformedBy())
            ->setPet($operationHistory->getPet());

        return $operationHistoryDto;
    }
}