<?php

namespace App\Controller;

use App\Model\PetForm\PetFormType;
use App\Pet\PetUseCase;
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

    #[Route('/pet/add', name: 'app_pet_web')]
    public function createFromForm(Request $request): Response
    {
        $form = $this->createForm(PetFormType::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $this->petUseCase->create($name, $description);

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

        if (!$pet) {
            throw $this->createNotFoundException('Питомец не найден');
        }

        $form = $this->createForm(PetFormType::class, $pet);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $this->petUseCase->update( $id,
                $form->get('name')->getData(),
                $form->get('description')->getData());

            return $this->redirectToRoute('app_pet_list');
        }

        return $this->render('pet_web/updatePetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
