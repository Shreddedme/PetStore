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
use App\ApiFilter\UserDtoFilter;
use App\Processor\UserCreateProcessor;
use App\Processor\UserDeleteProcessor;
use App\Processor\UserUpdateProccesor;
use App\Provider\UserListProvider;
use App\Provider\UserProvider;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/user',
            provider: UserListProvider::class,
        ),
        new Get(
            uriTemplate: '/user/{id}',
            provider: UserProvider::class,
        ),
        new Post(
            uriTemplate: '/user',
            processor: UserCreateProcessor::class,
        ),
        new Put(
            uriTemplate: '/user/{id}',
            denormalizationContext:['groups' => [self::USER_PUT]],
            provider: UserProvider::class,
            processor: UserUpdateProccesor::class,
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            provider: UserProvider::class,
            processor: UserDeleteProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => [self::USER_READ]],
    denormalizationContext: ['groups' => [self::USER_WRITE]],
)]
#[ApiFilter(UserDtoFilter::class)]
class UserDto
{
    const USER_READ = 'user:read';
    const USER_WRITE = 'user:write';
    const USER_PUT = 'user:put';

    #[Groups([self::USER_READ, self::USER_WRITE, self::USER_PUT])]
    #[ApiProperty(openapiContext: ['example' => 1])]
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
    #[Groups([self::USER_READ, self::USER_WRITE, self::USER_PUT])]
    #[ApiProperty(openapiContext: ['example' => 'John'])]
    private string $name;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[ApiProperty(openapiContext: ['example' => '123456'])]
    #[Groups([self::USER_WRITE, self::USER_PUT])]
    private string $password;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Groups([self::USER_READ, self::USER_WRITE, self::USER_PUT])]
    #[ApiProperty(openapiContext: ['example' => 'example@mail.ru'])]
    private string $email;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Choice(
        choices: ["ROLE_USER", "ROLE_ADMIN"],
        multiple: true,
        message: "Invalid role provided."
    )]
    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string")
     * )
     */
    #[Groups([self::USER_READ, self::USER_WRITE, self::USER_PUT])]
    #[ApiProperty(openapiContext: ['example' => ["ROLE_USER"]])]
    private array $roles;

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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}