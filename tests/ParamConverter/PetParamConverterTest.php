<?php

namespace App\Tests\ParamConverter;

use App\Model\Dto\PetDto;
use App\ParamConverter\PetParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;

class PetParamConverterTest extends TestCase
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
        $petDto = new PetDto();
        $request = new Request([], [], [], [], [], [], json_encode($petDto));
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configuration = new ParamConverter(['name' => 'petDto']);

        $this->serializer->method('deserialize')->willReturn($petDto);

        $petParamConverter = new PetParamConverter($this->serializer, $this->validator);
        $petParamConverter->apply($request, $configuration);

        $this->assertSame($petDto, $request->attributes->get('petDto'));
    }
}