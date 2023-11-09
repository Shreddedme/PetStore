<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OperationHistoryRepository;
use App\Service\Provider\OperationHistoryProviderActionCountForDate;
use App\Service\Provider\OperationHistoryProviderLatestActions;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OperationHistoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/operationHistory/getLatestActions',
            normalizationContext: ['groups' => [self::OPERATION_HISTORY_GET, self::USER_SHORT_GET_GROUP]],
            provider: OperationHistoryProviderLatestActions::class,
        ),
        new GetCollection(
            uriTemplate: '/operationHistory/getActionCountForDate',
            normalizationContext: ['groups' => [self::OPERATION_HISTORY_GET, self::USER_SHORT_GET_GROUP]],
            provider: OperationHistoryProviderActionCountForDate::class,
        )
    ]
)]
#[ORM\Index(columns: ['operation_date'], name: 'operation_date_idx')]
class OperationHistory
{
    const OPERATION_HISTORY_GET = 'get';
    const USER_SHORT_GET_GROUP = 'user:getShort';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(self::OPERATION_HISTORY_GET)]
    private ?int $id = null;

    #[ORM\Column(name: 'operation_date', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(self::OPERATION_HISTORY_GET)]
    private DateTime $operationDate;

    #[ORM\ManyToOne(inversedBy: 'operationHistories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(self::OPERATION_HISTORY_GET)]
    private User $performedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function setPerformedBy(?User $performedBy): self
    {
        $this->performedBy = $performedBy;

        return $this;
    }
}
