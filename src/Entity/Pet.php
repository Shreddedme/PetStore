<?php

namespace App\Entity;

use App\Repository\PetRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Annotations as OA;
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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
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
    /**
     * @ORM\Column(type="string")
     * @Groups({"pet:create", "pet:read"})
     */
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
    /**
     * @ORM\Column(type="string")
     * @Groups({"pet:create", "pet:read"})
     */
    private string $description;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    /**
     * @ORM\Column(type="DateTime")
     * @Groups({"pet:create", "pet:read"})
     */
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'update')]
    /**
     * @ORM\Column(type="DateTime")
     * @Groups({"pet:create", "pet:read"})
     */
    private DateTime $updatedAt;

    #[ORM\Column(type: 'integer', nullable: false)]
    /**
     * @ORM\Column(type="integer")
     * @Groups({"pet:create", "pet:read"})
     */
    private int $createdBy;

    #[ORM\Column(type: 'integer', nullable: true)]
    /**
     * @ORM\Column(type="integer")
     * @Groups({"pet:create", "pet:read"})
     */
    private ?int $updatedBy = null;

    #[ORM\ManyToOne(inversedBy: 'pet', fetch: 'EAGER' )]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @ORM\Column(type="integer")
     * @Groups({"pet:create", "pet:read"})
     */
    private User $owner;

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

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?int $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
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
}
