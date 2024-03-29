<?php

namespace App\Tests\ParamConverter;

use App\Exception\ValidationException;
use App\Model\Dto\PetDto;
use App\ParamConverter\PetParamConverter;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PetParamConverterTest extends TestCase
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
        $petDto = new PetDto();
        $request = new Request([], [], [], [], [], [], json_encode($petDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willReturn($petDto);

        $petParamConverter = new PetParamConverter($this->serializer, $this->validator);
        $petParamConverter->apply($request, $configuration);

        $this->assertSame($petDto, $request->attributes->get('petDto'));
    }

    public function testSupports(): void
    {
        $configuration = new ParamConverter(['class' => PetDto::class]);

        $petParamConverter = new PetParamConverter($this->serializer, $this->validator);
        $supports = $petParamConverter->supports($configuration);

        $this->assertTrue($supports);
    }

    public function testApplyThrowsValidationException(): void
    {
        $petDto = new PetDto();
        $request = new Request([], [], [], [], [], [], json_encode($petDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willReturn($petDto);
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([new ConstraintViolation('Test error', '', [], '', '', '')]));

        $petParamConverter = new PetParamConverter($this->serializer, $this->validator);

        $this->expectException(ValidationException::class);

        $petParamConverter->apply($request, $configuration);
    }

    public function testApplyThrowsBadRequestHttpException(): void
    {
        $request = new Request([], [], [], [], [], [], 'invalid json');
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willThrowException(new NotEncodableValueException());

        $petParamConverter = new PetParamConverter($this->serializer, $this->validator);

        $this->expectException(BadRequestHttpException::class);

        $petParamConverter->apply($request, $configuration);
    }
}