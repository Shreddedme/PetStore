<?php

namespace App\Controller;

use App\Enum\ApiGroupsEnum;
use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserCombinedDto;
use App\Model\UserForm\UserFormType;
use App\Service\User\UserUseCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserWebController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,

    )
    {}

    #[Route('/user/menu', name: 'app_user_menu')]
    public function main(): Response
    {
        return $this->render('user_web/userMenu.html.twig');
    }
    #[Route('/user/add', name: 'app_user_web')]
    public function createFromForm(Request $request): Response
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userUseCase->create($data);

            return $this->redirectToRoute(ApiGroupsEnum::USER_LIST->value);
        }

        return $this->render('user_web/createUserBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("userCombinedDto", class=UserCombinedDto::class, converter="user_combined_param_converter")
     * @throws \Exception
     */
    #[Route('/user/search', name: 'app_user_list')]
    public function getList(UserCombinedDto $userCombinedDto): Response
    {
        $paginator = $this->userUseCase->getAllUsers($userCombinedDto);
        $users = $paginator->getIterator();

        return $this->render('user_web/user_web_search/userListSearch.html.twig', [
            'users' => $users,
            'paginator' => $paginator,
            'userCombinedDto' => $userCombinedDto,
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/user/{id}', name: 'app_user_update')]
    public function update(Request $request, int $id): Response
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userUseCase->update($id, $data);

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user_web/updateUserBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
