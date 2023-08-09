<?php

namespace App\Controller;

use App\Model\Dto\PetDto;
use App\Model\PetForm\PetFormType;
use App\Service\Pet\PetUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PetWebController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
    )
    {}
    #[Route('/pet/menu')]
    public function main(): Response
    {
        return $this->render('pet_web/menuPet.html.twig');
    }

    #[Route('/pet/add', name: 'app_pet_web')]
    public function createFromForm(Request $request): Response
    {
        $form = $this->createForm(PetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $petDto = new PetDto();
            $petDto->setName($form->get('name')->getData());
            $petDto->setDescription($form->get('description')->getData());
            $petDto->setCreatedBy($form->get('createdBy')->getData());
            $this->petUseCase->create($petDto);

            return $this->redirectToRoute('app_pet_list');
        }

        return $this->render('pet_web/createPetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pet', name: 'app_pet_list')]
    public function getList(): Response
    {
        $pets = $this->petUseCase->findAll();

        return $this->render('pet_web/getListPetBootstrap.html.twig', [
            'pets' => $pets,
        ]);
    }

    #[Route('/pet/{id}', name: 'app_pet_update')]
    public function update(Request $request, int $id): Response
    {
        $pet = $this->petUseCase->find($id);
        $form = $this->createForm(PetFormType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $petDto = new PetDto();
            $petDto->setName($form->get('name')->getData());
            $petDto->setDescription($form->get('description')->getData());
            $petDto->setCreatedBy($form->get('createdBy')->getData());
            $this->petUseCase->update($id, $petDto);

            return $this->redirectToRoute('app_pet_list');
        }

        return $this->render('pet_web/updatePetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
