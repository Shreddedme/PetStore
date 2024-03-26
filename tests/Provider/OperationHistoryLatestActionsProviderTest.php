<?php

namespace App\Tests\Provider;

use ApiPlatform\Metadata\Operation;
use App\Entity\OperationHistory;
use App\Repository\OperationHistoryRepository;
use App\Service\Provider\OperationHistoryLatestActionsProvider;
use PHPUnit\Framework\TestCase;

class OperationHistoryLatestActionsProviderTest extends TestCase
{
    private OperationHistoryRepository $operationHistoryRepository;
    private OperationHistoryLatestActionsProvider $operationHistoryLatestActionsProvider;

    public function setUp(): void
    {
        $this->operationHistoryRepository = $this->createMock(OperationHistoryRepository::class);
        $this->operationHistoryLatestActionsProvider = new OperationHistoryLatestActionsProvider($this->operationHistoryRepository);
    }

    /**
     * @test
     * @covers OperationHistoryLatestActionsProvider::provide
     */
    public function provide(): void
    {
        $operation = $this->createMock(Operation::class);
        $operationHistory = $this->createMock(OperationHistory::class);

        $this->operationHistoryRepository->expects($this->once())
            ->method('findLatestOperationsByPet')
            ->willReturn([$operationHistory]);

        $result = $this->operationHistoryLatestActionsProvider->provide($operation);

        $this->assertEquals([$operationHistory], $result);
    }
}