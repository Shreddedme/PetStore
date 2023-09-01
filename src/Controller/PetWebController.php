<?php

namespace App\Controller;

use App\Model\Dto\PetSearchDto;
use App\Model\PetForm\PetFormType;
use App\Model\PetForm\PetSearchType;
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

    /**
     * @throws \Exception
     */
    #[Route('/pet/search', name: 'app_pet_search')]
    public function getByFilters(Request $request): Response
    {
        $form = $this->createForm(PetSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $petSearchDto = $form->getData();
            $request->getSession()->set('petSearchDto', $petSearchDto);
        } else {
            $petSearchDto = $request->getSession()->get('petSearchDto');
            if ($petSearchDto === null) {
                $petSearchDto = new PetSearchDto();
            }
        }

        $currentPage = $request->query->getInt('page', 1);
        $paginator = $this->petUseCase->findByFilter($petSearchDto, $currentPage);
        $pets = $paginator->getIterator();

        return $this->render('pet_web/pet_web_search/listSearchResults.html.twig', [
            'form' => $form->createView(),
            'pets' => $pets,
            'paginator' => $paginator,
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
    #[Route('/pet/delete/{id}', name: 'app_pet_delete')]
    public function delete(int $id): Response
    {
        $this->petUseCase->delete($id);

        return $this->redirectToRoute('app_pet_list');
    }
}
