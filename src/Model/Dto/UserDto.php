<?php

namespace App\Model\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\User;
use App\Processor\UserCreateProcessor;
use App\Processor\UserUpdateProccesor;
use App\Provider\UserProvider;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users',
            class: User::class,
        ),
        new Get(
            uriTemplate: '/user/{id}',
            output: UserDto::class,
            provider: UserProvider::class,
        ),
        new Post(
            uriTemplate: '/user/create',
            security: 'is_authenticated()',
            input: UserDto::class,
            output: UserDto::class,
            processor: UserCreateProcessor::class,
        ),
        new Put(
            uriTemplate: '/user/update',
            class: User::class,
            denormalizationContext:['groups' => ['put']],
            security: 'is_authenticated()',
            input: UserDto::class,
            output: UserDto::class,
            processor: UserUpdateProccesor::class,
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            class: User::class,
            security: 'is_authenticated()',
        ),
    ],
    stateless: false,
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class UserDto
{
    #[ApiProperty(openapiContext: ['example' => 1])]
    #[Groups('read', 'put')]
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
    #[ApiProperty(openapiContext: ['example' => 'John'])]
    private string $name;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Groups('write')]
    #[ApiProperty(openapiContext: ['example' => '123456'])]
    private string $password;

    #[Assert\NotBlank(message: 'Empty field')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Field cant be longer than 255'
    )]
    #[Groups(['read', 'write', 'put'])]
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
    #[Groups(['read', 'write', 'put'])]
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