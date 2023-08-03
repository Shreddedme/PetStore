<?php

namespace App\UserDto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;


class UserRequestDto
{
   public function __construct(
       public readonly string $name,
       public readonly string $roles,
   )
   {}
}