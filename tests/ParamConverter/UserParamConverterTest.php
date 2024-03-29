<?php

namespace App\Tests\ParamConverter;

use App\Model\Dto\UserDto;
use App\ParamConverter\UserParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;

class UserParamConverterTest extends TestCase
{
    private $serializer;
    private $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = Validation::createValidator();
    }

    public function testApply(): void
    {
        $userDto = new UserDto();
        $request = new Request([], [], [], [], [], [], json_encode($userDto));
        $configuration = new ParamConverter(['name' => 'userDto']);

        $this->serializer->method('deserialize')->willReturn($userDto);

        $userParamConverter = new UserParamConverter($this->serializer, $this->validator);
        $userParamConverter->apply($request, $configuration);

        $this->assertSame($userDto, $request->attributes->get('userDto'));
    }
}