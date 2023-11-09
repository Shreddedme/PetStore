<?php

namespace App\Service\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\OperationHistoryRepository;

class OperationHistoryProviderLatestActions implements ProviderInterface
{
    public function __construct(
        private OperationHistoryRepository $operationHistoryRepository,
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->operationHistoryRepository->getLatestActions();
    }
}