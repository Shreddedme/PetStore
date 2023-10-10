<?php
namespace App\Model\Dto;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiFilter\PetDtoFilter;
use App\Entity\User;
use App\Processor\PetCreateProcessor;
use App\Processor\PetDeleteProcessor;
use App\Processor\PetUpdateProcessor;
use App\Provider\PetListProvider;
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
            uriTemplate: '/pet',
            normalizationContext: ['groups' => [self::PET_GET, self::USER_SHORT_GET_GROUP]],
            provider: PetListProvider::class,
        ),
        new Get(
            uriTemplate: '/pet/{id}',
            normalizationContext: ['groups' => [self::PET_GET,  self::USER_SHORT_GET_GROUP]],
            provider: PetProvider::class,
        ),
        new Post(
            uriTemplate: '/pet',
            normalizationContext: ['groups' => [self::PET_GET, self::USER_SHORT_GET_GROUP]],
            processor: PetCreateProcessor::class,
        ),
        new Put(
            uriTemplate: '/pet/{id}',
            normalizationContext: ['groups' => [self::PET_GET, self::USER_SHORT_GET_GROUP]],
            denormalizationContext:['groups' => [self::PET_PUT]],
            provider: PetProvider::class,
            processor: PetUpdateProcessor::class,
        ),
        new Delete(
            uriTemplate: '/pet/{id}',
            provider: PetProvider::class,
            processor: PetDeleteProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => [self::PET_READ]],
    denormalizationContext: ['groups' => [self::PET_WRITE]],
)]
#[ApiFilter(PetDtoFilter::class)]
class PetDto
{
    const PET_GET = 'get';
    const PET_PUT = 'put';
    const PET_READ = 'read';
    const PET_WRITE = 'write';
    const USER_SHORT_GET_GROUP = 'user:getShort';

    #[ApiProperty(openapiContext: ['example' => 1])]
    #[Groups([self::PET_READ, self::PET_GET])]
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
    #[Groups([self::PET_READ, self::PET_WRITE, self::PET_PUT, self::PET_GET])]
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
    #[Groups([self::PET_READ, self::PET_WRITE, self::PET_PUT, self::PET_GET])]
    #[ApiProperty(openapiContext: ['example' => 'Lazy'])]
    private string $description;

    #[Gedmo\Timestampable(on: 'create')]
    #[Groups([self::PET_READ, self::PET_GET])]
    private ?DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[Groups([self::PET_READ, self::PET_GET])]
    private DateTime $updatedAt;

    #[ApiProperty(openapiContext: ['example' => 1])]
    #[Groups([self::PET_READ, self::PET_WRITE, self::PET_GET])]
    private int $createdBy;

    #[Groups([self::PET_READ, self::PET_GET])]
    private ?int $updatedBy = null;

    #[Groups([self::PET_READ, self::PET_WRITE, self::PET_GET])]
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