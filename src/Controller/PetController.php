<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\PetDto;
use App\Model\Dto\PetCombinedDto;
use App\Service\Pet\PetUseCase;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PetController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
        private SerializerInterface $serializer,
        private CacheInterface $cache,
    )
    {}

    #[Route('/api/pet/add', methods: ['POST'])]
    /**
     * @ParamConverter("petDto", class=PetDto::class, converter="pet_param_converter")
     * @OA\Tag(name="Pet")
     * @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", default="Dog"),
     *              @OA\Property(property="description", type="string", default="German Shepherd"),
     *              @OA\Property(property="createdBy", type="integer", default=1)
     *          )
     *      ),
     * @OA\Response(
     *          response=201,
     *          description="Питомец создан",
     *          @OA\JsonContent(ref=@Model(type=\App\Model\Dto\PetDto::class))
     *      ),
     * @OA\Response(
     *       response=400,
     *       description="Неверные данные",
     *       @OA\JsonContent(ref=@Model(type=\App\Model\ErrorHandling\ErrorResponse::class))
     *  )
     */
    public function create(PetDto $petDto): Response
    {
        $this->petUseCase->create($petDto);

        return $this->json([
            $petDto,
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/pet/{id}', methods: 'GET')]
    /**
     * @ParamConverter("pet", class="App\Entity\Pet")
     * @OA\Tag(name="Pet")
     * @OA\Response(
     *           response=200,
     *           description="Получили питомца",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\Pet::class))
     *       )
     */
    public function getOne(Pet $pet): JsonResponse
    {
        return $this->json($pet);
    }

    /**
     * @ParamConverter("petCombinedDto", class=PetCombinedDto::class, converter="pet_combined_param_converter")
     * @OA\Tag(name="Pet")
     * @OA\Get(
     * path="/api/pets",
     * summary="Получить список питомцев с фильтрами",
     * tags={"Pet"},
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Имя питомца",
     * required=false,
     * @OA\Schema(type="string", default="cat")
     * ),
     * @OA\Parameter(
     * name="description",
     * in="query",
     * description="Описание питомца",
     * required=false,
     * @OA\Schema(type="string", default="small")
     * ),
     * @OA\Parameter(
     * name="owner",
     * in="query",
     * description="Имя владельца",
     * required=false,
     * @OA\Schema(type="string", default="John")
     * ),
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Номер страницы",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Список питомцев",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref=@Model(type=\App\Model\Dto\PetCombinedDto::class))
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Неверные данные",
     * @OA\JsonContent(ref=@Model(type=\App\Model\ErrorHandling\ErrorResponse::class))
     * )
     * )
     * @param PetCombinedDto $petCombinedDto
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/pets', methods: ['GET'])]
    public function getByFilters(PetCombinedDto $petCombinedDto): JsonResponse
    {
       return $this->json($this->petUseCase->findByFilter($petCombinedDto));
    }

    #[Route('/api/pet/{id}', name: 'update_method', methods: 'PUT')]
    /**
     * @ParamConverter("petDto", class=PetDto::class, converter="pet_param_converter")
     * @OA\Tag(name="Pet")
     * @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(
     *               @OA\Property(property="name", type="string", default="dog"),
     *               @OA\Property(property="description", type="string", default="German Shepherd")
     *           )
     *       ),
     * @OA\Response(
     *           response=200,
     *           description="Питомец обновлен",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\Pet::class))
     *       )
     * @throws EntityNotFoundException
     */
    public function update(PetDto $petDto, int $id): JsonResponse
    {
        $pet = $this->petUseCase->update($id, $petDto);

        return $this->json($pet);
    }

    #[Route('/api/pet/{id}', name:'api_delete',  methods: 'DELETE')]
    /**
     * @OA\Tag(name="Pet")
     */
    public function delete(int $id): Response
    {
        $this->petUseCase->delete($id);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
