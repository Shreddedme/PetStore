<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\OperationHistoryRepository;
use App\Transformer\OperationHistoryTransformer;

class OperationHistoryProviderLatestActions implements ProviderInterface
{
    public function __construct(
        private OperationHistoryRepository $operationHistoryRepository,
        private OperationHistoryTransformer $operationHistoryTransformer,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $operationHistories = $this->operationHistoryRepository->findLatestOperations();
        $operationHistoriesDtos = [];

        foreach ($operationHistories as $operationHistory) {
            $operationHistoriesDtos[] = $this->operationHistoryTransformer->toDto($operationHistory);
        }

        return $operationHistoriesDtos;
    }
}