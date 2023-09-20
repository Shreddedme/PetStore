<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\Dto\UserDto;
use App\Service\User\UserUseCase;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,
        private SerializerInterface $serializer,
    )
    {}

     #[Route('/api/user/add',name:'api_user_add', methods:'POST')]
     /**
      * @ParamConverter("userDto", class=UserDto::class, converter="user_param_converter")
      * @OA\Tag(name="User")
      * @OA\RequestBody(
      *          required=true,
      *          @OA\JsonContent(
      *              @OA\Property(property="name", type="string", default="John"),
      *              @OA\Property(property="roles", type="string", default="guest"),
      *          )
      *      ),
      * @OA\Response(
      *          response=201,
      *          description="Пользователь создан",
      *          @OA\JsonContent(ref=@Model(type=\App\Model\Dto\UserDto::class))
      *      ),
      * @OA\Response(
      *       response=400,
      *       description="Неверные данные",
      *       @OA\JsonContent(ref=@Model(type=\App\Model\ErrorHandling\ErrorResponse::class))
      *  )
      */
    public function create(UserDto $userDto): JsonResponse
    {
        $this->userUseCase->create($userDto);

        return $this->json([
            $userDto,
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/user/{id}', methods: 'GET')]
    /**
     * @ParamConverter("user", class="App\Entity\User")
     * @OA\Tag(name="User")
     * @OA\Response(
     *           response=200,
     *           description="Получили пользователя",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\User::class))
     *       )
     */
    public function getOne(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/api/user', methods: 'GET')]
    public function getList(): JsonResponse
    {
        $users = $this->userUseCase->findAll();

        $usersJson = $this->serializer->serialize($users, 'json');

        return new JsonResponse($usersJson, 200, [], true);
    }

    #[Route('/api/user/{id}', name: 'user_update_method', methods: 'PUT')]
    /**
     * @ParamConverter("userDto", class=UserDto::class, converter="user_param_converter")
     * @OA\Tag(name="User")
     * @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(
     *               @OA\Property(property="name", type="string", default="john"),
     *               @OA\Property(property="roles", type="string", default="admin")
     *           )
     *       ),
     * @OA\Response(
     *           response=200,
     *           description="Пользователь обновлен",
     *           @OA\JsonContent(ref=@Model(type=\App\Entity\User::class))
     *       )
     */
    public function update(UserDto $userDto, int $id): JsonResponse
    {
        $user = $this->userUseCase->update($id, $userDto);

        return $this->json($user);
    }

    #[Route('/api/user/{id}', methods: 'DELETE')]
    /**
     * @OA\Tag(name="User")
     */
    public function delete(int $id): Response
    {
        $this->userUseCase->delete($id);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
