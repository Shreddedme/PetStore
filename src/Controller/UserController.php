<?php

namespace App\Controller;

use App\UserDto\UserRequestDto;
use  App\UserDto\UserResponseDto;
use App\User\UserUseCase;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,
    )
    {}

     #[Route('/api/user/add', methods:'POST')]
     /**
      * Создать пользователя
      * @OA\Tag(name="User")
      * @ParamConverter(
      *       "userRequestDto",
      *       class=UserRequestDto::class,
      *       options={"mapping": {"name": "name", "roles": "roles"}}
      *   ),
      * @OA\RequestBody(
      *      required=true,
      *      @Model(type=UserRequestDto::class)
      *  )
      * @OA\Response(
      *      response=200,
      *      description="Пользователь создан",
      *      @OA\JsonContent(ref=@Model(type=UserResponseDto::class))
      *  ),
      * @OA\Response(
      *       response=400,
      *       description="Ошибка валидации",
      *       @OA\JsonContent(ref=@Model(type=UserResponseDto::class))
      *   ),
      * @return JsonResponse
      */
    public function create(UserRequestDto $userRequestDto): JsonResponse
    {
        $this->userUseCase->create($userRequestDto);
        return $this->json('user created');
    }
 /**
 *
 * @OA\Get(
 *     path="/api/user/{id}",
 *     tags={"User"},
 *     summary="Просмотр пользователя",
 *     @OA\Response(
 *         response=200,
 *         description="Успешно выводит пользователя"
 *     ),
 *     @OA\Tag(name="User")
 * )
 *
 * @param Request $request
 * @return JsonResponse
 */

    #[Route('/api/user/{id}', methods: 'GET')]
    public function getOne(int $id): JsonResponse
    {
        return $this->json($this->userUseCase->find($id));
    }
    /**
     *
     * @OA\Get(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Просмотр всех пользователей",
     *     @OA\Response(
     *         response=200,
     *         description="Успешно выводит список всех пользователей"
     *     ),
     *     @OA\Tag(name="User")
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/user', methods: 'GET')]
    public function getList(): JsonResponse
    {
        return $this->json($this->userUseCase->findAll());
    }
    #[Route('/api/user/{id}', methods: 'PUT')]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->userUseCase->update(
            $id,
            $request->get('name'),
            $request->get('roles')
        );

        return $this->json($user);
    }

    #[Route('/api/user/{id}', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $this->userUseCase->delete($id);

        return $this->json('user deleted');
    }
}
