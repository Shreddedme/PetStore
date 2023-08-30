<?php

namespace App\Controller;

use App\Model\Dto\PetDto;
use App\Model\PetForm\PetFormType;
use App\Service\Pet\PetUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PetWebController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
        private AuthorizationCheckerInterface $authorizationChecker,
    )
    {}
    #[Route('/pet/menu', name: 'app_pet_menu')]
    public function main(): Response
    {
        return $this->render('pet_web/menuPet.html.twig');
    }
    #[Route('/pet/select', name: 'app_select_menu')]
    public function selectActionAfterLogin(): Response
    {
        return $this->render('pet_web/afterLogin.html.twig');
    }

    #[Route('/pet/add', name: 'app_pet_web')]
    public function createFromForm(Request $request): Response
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException('You must be logged in to access this page');
        }

        $form = $this->createForm(PetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->petUseCase->create($data);

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
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException('You must be logged in to access this page');
        }

        $form = $this->createForm(PetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->petUseCase->update($id, $data);

            return $this->redirectToRoute('app_pet_list');
        }

        return $this->render('pet_web/updatePetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
