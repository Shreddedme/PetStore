<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OperationHistoryRepository;
use App\Service\Provider\OperationHistoryLatestActionsProvider;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OperationHistoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/operationHistory/getLatestActions',
            normalizationContext: ['groups' => [self::OPERATION_HISTORY_GET, self::USER_SHORT_GET_GROUP, self::PET_GROUP_GET]],
            provider: OperationHistoryLatestActionsProvider::class,
        ),
    ]
)]
#[ORM\Index(columns: ['operation_date'], name: 'operation_date_idx')]
class OperationHistory
{
    const OPERATION_HISTORY_GET = 'get';
    const USER_SHORT_GET_GROUP = 'user:getShort';
    const PET_GROUP_GET = 'pet:getShort';

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

    #[ORM\ManyToOne(inversedBy: 'operationHistory')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(self::OPERATION_HISTORY_GET)]
    private ?Pet $pet = null;

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

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(?Pet $pet): static
    {
        $this->pet = $pet;

        return $this;
    }
}
