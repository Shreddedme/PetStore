<?php

namespace App\Tests\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\PetRequestDto;
use App\ParamConverter\PetRequestParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetRequestParamConverterTest extends TestCase
{
    private $serializer;
    private $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(Serializer::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testApply(): void
    {
        $petRequestDto = new PetRequestDto();
        $request = new Request([], [], [], [], [], [], json_encode($petRequestDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petRequestDto']);

        $this->serializer->method('denormalize')->willReturn($petRequestDto);

        $petRequestParamConverter = new PetRequestParamConverter($this->serializer, $this->validator);
        $petRequestParamConverter->apply($request, $configuration);

        $this->assertSame($petRequestDto, $request->attributes->get('petRequestDto'));
    }

    public function testApplyThrowsValidationException(): void
    {
        $petRequestDto = new PetRequestDto();
        $request = new Request([], [], [], [], [], [], json_encode($petRequestDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('denormalize')->willReturn($petRequestDto);
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([new ConstraintViolation('Test error', '', [], '', '', '')]));

        $petParamConverter = new PetRequestParamConverter($this->serializer, $this->validator);

        $this->expectException(ValidationException::class);

        $petParamConverter->apply($request, $configuration);
    }

    public function testApplyThrowsBadRequestHttpException(): void
    {
        $request = new Request([], [], [], [], [], [], 'invalid json');
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('denormalize')->willThrowException(new NotEncodableValueException());

        $petParamConverter = new PetRequestParamConverter($this->serializer, $this->validator);

        $this->expectException(BadRequestHttpException::class);

        $petParamConverter->apply($request, $configuration);
    }
}