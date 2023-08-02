<?php

namespace App\Controller;

use App\User\UserUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,
    )
    {}

    #[Route('/api/user/add', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $roles = $request->get('roles');
        $this->userUseCase->create($name, $roles);

        return $this->json('user created');
    }

    #[Route('/api/user/{id}', methods: 'GET')]
    public function getOne(int $id): JsonResponse
    {
        return $this->json($this->userUseCase->find($id));
    }

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
