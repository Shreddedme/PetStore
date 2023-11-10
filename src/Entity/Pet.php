<?php

namespace App\Entity;

use App\Repository\PetRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     title="Pet",
 *     description="Питомец",
 *     @OA\Property(property="id", type="integer", default=1),
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="description", type="string", default="Very lazy"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time"),
 *     @OA\Property(property="createdBy", type="integer", default=1),
 *     @OA\Property(property="updatedBy", type="integer", default=1),
 *     @OA\Property(
 *          property="owner",
 *          type="object",
 *          @OA\Property(property="name", type="string", default="john"),
 *      ),
 * )
 */
#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    public const PET_GET_GROUP = 'pet:get';
    public const PET_SHORT_GROUP =  'pet:getShort';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(self::PET_GET_GROUP)]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cannot be entered'
    )]
    #[Groups([self::PET_GET_GROUP, self::PET_SHORT_GROUP])]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cannot be entered'
    )]
    #[Groups(self::PET_GET_GROUP)]
    private string $description;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(self::PET_GET_GROUP)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'update')]
    #[Groups(self::PET_GET_GROUP)]
    private DateTime $updatedAt;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Groups(self::PET_GET_GROUP)]
    private int $createdBy;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(self::PET_GET_GROUP)]
    private ?int $updatedBy = null;

    #[ORM\ManyToOne(inversedBy: 'pet', fetch: 'EAGER' )]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(self::PET_GET_GROUP)]
    private User $owner;

    #[ORM\OneToMany(mappedBy: 'pet', targetEntity: OperationHistory::class)]
    private Collection $operationHistory;

    public function __construct()
    {
        $this->operationHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?int $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, OperationHistory>
     */
    public function getOperationHistory(): Collection
    {
        return $this->operationHistory;
    }

    public function addOperationHistory(OperationHistory $operationHistory): static
    {
        if (!$this->operationHistory->contains($operationHistory)) {
            $this->operationHistory->add($operationHistory);
            $operationHistory->setPet($this);
        }

        return $this;
    }

    public function removeOperationHistory(OperationHistory $operationHistory): static
    {
        if ($this->operationHistory->removeElement($operationHistory)) {
            // set the owning side to null (unless already changed)
            if ($operationHistory->getPet() === $this) {
                $operationHistory->setPet(null);
            }
        }

        return $this;
    }
}
