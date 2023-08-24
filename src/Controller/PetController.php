<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Model\Dto\PetDto;
use App\Service\Pet\PetUseCase;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Serializer;

class PetController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
        private Serializer $serializer,
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

    #[Route('/api/pet', methods: 'GET')]
    /**
     * @OA\Tag(name="Pet")
     * @OA\Response(
     *           response=200,
     *           description="Список питомцев",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\Pet::class))
     *       )
     */
    public function getList(): JsonResponse
    {
        $pets = $this->petUseCase->findAll();

        $petsJson = $this->serializer->serialize($pets, 'json');

        return new JsonResponse($petsJson, 200, [], true);
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
     *           response=201,
     *           description="Питомец обновлен",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\Pet::class))
     *       )
     */
    public function update(PetDto $petDto, int $id): JsonResponse
    {
        $pet =  $this->petUseCase->update($id, $petDto);

        return $this->json($pet);
    }

    #[Route('/api/pet/{id}', methods: 'DELETE')]
    /**
     * @OA\Tag(name="Pet")
     * @OA\Response(
     *     response=204,
     *     description="Питомец удален",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="object")
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $this->petUseCase->delete($id);

        return $this->json('pet deleted');
    }
}
