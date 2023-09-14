<?php

namespace App\Controller;

use App\Exception\EntityNotFoundException;
use App\Model\PetForm\PetFormType;
use App\Model\PetForm\PetSearchType;
use App\Service\Pet\PetUseCase;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Model\Dto\PetCombinedDto;

class PetWebController extends AbstractController
{
    public function __construct(
        private PetUseCase $petUseCase,
        private AuthorizationCheckerInterface $authorizationChecker,
        private CacheInterface $cache,
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

            return $this->redirectToRoute('app_pet_search');
        }

        return $this->render('pet_web/createPetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("petCombinedDto", class=PetCombinedDto::class, converter="pet_combined_param_converter")
     * @throws Exception
     */
    #[Route('/pet/search', name: 'app_pet_search')]
    public function getByFilters(Request $request, PetCombinedDto $petCombinedDto): Response
    {
        $form = $this->createFormBuilder(PetSearchType::class)
            ->setMethod('GET')
            ->getForm();

        $form->handleRequest($request);

        $paginator = $this->petUseCase->findByFilter($petCombinedDto);
        $pets = $paginator->getIterator();

        return $this->render('pet_web/pet_web_search/listSearchResults.html.twig', [
            'form' => $form->createView(),
            'pets' => $pets,
            'paginator' => $paginator,
            'sortBy' => $petCombinedDto->getSortBy(),
            'sortDirection' => $petCombinedDto->getSortDirection(),
            'petCombinedDto' => $petCombinedDto,

        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
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

            return $this->redirectToRoute('app_pet_search');
        }

        return $this->render('pet_web/updatePetBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/pet/delete/{id}', name: 'app_pet_delete')]
    public function delete(Request $request, int $id): Response
    {
        $this->petUseCase->delete($id);
        $this->cache->delete('search_pet_list_cache');

        return $this->redirectToRoute('app_pet_search');
    }
}
