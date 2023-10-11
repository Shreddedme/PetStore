<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserDto;
use App\Service\User\UserUseCase;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\Dto\UserCombinedDto;

class UserController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,
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
      *              @OA\Property(property="password", type="string", default="123456"),
      *              @OA\Property(property="email", type="string", default="yandex@mail.ru"),
      *              @OA\Property(
      *                  property="roles",
      *                  type="array",
      *                  @OA\Items(type="string", default="guest")
      *              ),
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
        return $this->json(
            $this->userUseCase->create($userDto),
            Response::HTTP_OK,
            [],
            ['groups' => UserDto::USER_READ]
        );
    }

    #[Route('/api/user/{id}', methods: 'GET')]
    /**
     * @ParamConverter("user", class="App\Entity\User")
     * @OA\Tag(name="User")
     * @OA\Response(
     *           response=200,
     *           description="Получили пользователя",
     *           @OA\JsonContent(ref=@Model(type=\App\Model\Dto\UserDto::class))
     * )
     */
    public function getOne(User $user): JsonResponse
    {
        return $this->json(
            $this->userUseCase->getOne($user),
            Response::HTTP_OK,
            [],
            ['groups' => UserDto::USER_READ]
        );
    }

    /**
     * @ParamConverter("userCombinedDto", class=UserCombinedDto::class, converter="user_combined_param_converter")
     * @OA\Tag(name="User")
     * @OA\Get(
     *      path="/api/user",
     *      summary="Получить список пользователей",
     *      tags={"User"},
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Номер страницы",
     *          required=false,
     *          @OA\Schema(type="int", default="1")
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Список пользователей",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=\App\Model\Dto\UserCombinedDto::class))
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Неверные данные",
     *          @OA\JsonContent(ref=@Model(type=\App\Model\ErrorHandling\ErrorResponse::class))
     *      )
     *  )
     */
    #[Route('/api/user', methods: 'GET')]
    public function getList(UserCombinedDto $userCombinedDto): JsonResponse
    {
        return $this->json(
            $this->userUseCase->getAllUsers($userCombinedDto),
            Response::HTTP_OK,
            [],
            ['groups' => User::USER_GET_GROUP]
        );
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
     *           @OA\JsonContent(ref=@Model(type=\App\Model\Dto\UserDto::class))
     *       )
     * @throws EntityNotFoundException
     */
    public function update(UserDto $userDto, int $id): JsonResponse
    {
        return $this->json(
            $this->userUseCase->update($id, $userDto),
            Response::HTTP_OK,
            [],
            ['groups' => UserDto::USER_READ]
        );
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
