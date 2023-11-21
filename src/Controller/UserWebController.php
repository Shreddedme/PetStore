<?php

namespace App\Controller;

use App\Exception\EntityNotFoundException;
use App\Model\Dto\UserRequestDto;
use App\Model\UserForm\UserFormType;
use App\Service\User\UserUseCase;
use App\Transformer\UserTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserWebController extends AbstractController
{
    public function __construct(
        private UserUseCase $userUseCase,
        private UserTransformer $userTransformer,
    )
    {}

    /**
     * @ParamConverter("userRequestDto", class=UserRequestDto::class, converter="user_request_param_converter")
     * @throws \Exception
     */
    #[Route('/user/search', name: 'app_user_list')]
    public function getList(UserRequestDto $userRequestDto): Response
    {
        $paginator = $this->userUseCase->getAllUsers($userRequestDto);
        $users = $paginator->getIterator();

        return $this->render('user_web/user_web_search/userListSearch.html.twig', [
            'users' => $users,
            'paginator' => $paginator,
            'userRequestDto' => $userRequestDto,
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
            $userDto = $this->userTransformer->toDto($data);
            $this->userUseCase->update($id, $userDto);

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user_web/updateUserBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
