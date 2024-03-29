<?php

namespace App\Tests\ParamConverter;

use App\Model\Dto\UserRequestDto;
use App\ParamConverter\UserRequestParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;

class UserRequestParamConverterTest extends TestCase
{
    private $serializer;
    private $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['denormalize'])
            ->getMock();
        $this->validator = Validation::createValidator();
    }

    public function testApply(): void
    {
        $userRequestDto = new UserRequestDto();
        $request = new Request([], [], [], [], [], [], json_encode($userRequestDto));
        $configuration = new ParamConverter(['name' => 'userRequestDto']);

        $this->serializer->method('denormalize')->willReturn($userRequestDto);

        $userRequestParamConverter = new UserRequestParamConverter($this->serializer, $this->validator);
        $userRequestParamConverter->apply($request, $configuration);

        $this->assertSame($userRequestDto, $request->attributes->get('userRequestDto'));
    }
}