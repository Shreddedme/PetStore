<?php

namespace App\Tests\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\UserDto;
use App\ParamConverter\UserParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserParamConverterTest extends TestCase
{
    private $serializer;
    private $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
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

    public function testApplyThrowsValidationException(): void
    {
        $petDto = new UserDto();
        $request = new Request([], [], [], [], [], [], json_encode($petDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willReturn($petDto);
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([new ConstraintViolation('Test error', '', [], '', '', '')]));

        $petParamConverter = new UserParamConverter($this->serializer, $this->validator);

        $this->expectException(ValidationException::class);

        $petParamConverter->apply($request, $configuration);
    }

    public function testApplyThrowsBadRequestHttpException(): void
    {
        $request = new Request([], [], [], [], [], [], 'invalid json');
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willThrowException(new NotEncodableValueException());

        $petParamConverter = new UserParamConverter($this->serializer, $this->validator);

        $this->expectException(BadRequestHttpException::class);

        $petParamConverter->apply($request, $configuration);
    }
}