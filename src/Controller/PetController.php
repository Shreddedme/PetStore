<?php

namespace App\Controller;

use App\Service\Pet\PetUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
    )
    {}

    #[Route('/api/pet/add', methods: 'POST')]
    public function create(Request $request): Response
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $this->petUseCase->create($name, $description);

        return $this->json('pet created');
    }

    #[Route('/api/pet/{id}', methods: 'GET')]
    public function getOne(int $id): JsonResponse
    {
        return $this->json($this->petUseCase->find($id));
    }

    #[Route('/api/pet', methods: 'GET')]
    public function getList(): Response
    {
        return $this->json($this->petUseCase->findAll());
    }
    #[Route('/api/pet/{id}', methods: 'PUT')]
    public function update(Request $request, int $id): JsonResponse
    {
        $pet = $this->petUseCase->update(
            $id,
            $request->get('name'),
            $request->get('description'),
        );

        return $this->json($pet);
    }

    #[Route('/api/pet/{id}', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $this->petUseCase->delete($id);

        return $this->json('pet deleted');
    }
}
