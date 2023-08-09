<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Model\Dto\PetDto;
use App\Service\Pet\PetUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PetController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
        private SerializerInterface $serializer,
    )
    {}

    #[Route('/api/pet/add', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $jsonContent = $request->getContent();
        $petDto = $this->serializer->deserialize($jsonContent, PetDto::class, 'json');
        $this->petUseCase->create($petDto);

        return $this->json('Pet created', Response::HTTP_CREATED);
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
