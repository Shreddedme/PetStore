<?php
namespace App\Model\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Pet;
use App\Entity\User;
use App\Processor\PetCreateProcessor;
use App\Processor\PetUpdateProcessor;
use App\Provider\PetProvider;
use DateTime;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @OA\Schema(
 *     title="PetDto",
 *     description="Питомец",
 *     @OA\Property(property="name", type="string", default="Cat"),
 *     @OA\Property(property="description", type="string", default="Very lazy"),
 *     @OA\Property(property="createdBy", type="integer", default=1)
 * )
 */
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/pets',
            class: Pet::class,
        ),
        new Get(
            uriTemplate: '/pet/{id}',
            output: PetDto::class,
            provider: PetProvider::class,
        ),
        new Post(
            uriTemplate: '/pet/create',
            security: 'is_authenticated()',
            input: PetDto::class,
            output: PetDto::class,
            processor: PetCreateProcessor::class,
        ),
        new Put(
            uriTemplate: '/pet/update',
            denormalizationContext:['groups' => ['put']],
            security: 'is_authenticated()',
            input: PetDto::class,
            output: PetDto::class,
            processor: PetUpdateProcessor::class,
        ),
        new Delete(
            uriTemplate: '/pet/{id}',
            class: Pet::class,
            security: 'is_authenticated()',
        ),
    ],
    stateless: false,
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write', 'patch']],
)]
class PetDto
{
    #[ApiProperty(openapiContext: ['example' => 1])]
    #[Groups(['read', 'put'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    #[Groups(['read', 'write', 'put'])]
    #[ApiProperty(openapiContext: ['example' => 'Cat'])]
    private string $name;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]*$/',
        message: 'Forbidden characters cant be entered'
    )]
    #[Groups(['read', 'write', 'put'])]
    #[ApiProperty(openapiContext: ['example' => 'Lazy'])]
    private string $description;

    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(['read'])]
    private ?DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[Groups(['read'])]
    private DateTime $updatedAt;

    #[ApiProperty(openapiContext: ['example' => 1])]
    #[Groups(['write'])]
    private int $createdBy;

    #[Groups(['read'])]
    private ?int $updatedBy = null;

    #[Groups(['read'])]
    private User $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
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

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }
}