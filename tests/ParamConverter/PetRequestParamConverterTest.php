<?php

namespace App\Tests\ParamConverter;

use App\Model\Dto\PetRequestDto;
use App\ParamConverter\PetRequestParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;

class PetRequestParamConverterTest extends TestCase
{
    private $serializer;
    private $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(Serializer::class);
        $this->validator = Validation::createValidator();
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
}