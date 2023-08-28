<?php

namespace App\Controller;

use App\Model\UserForm\UserFormType;
use App\Service\User\UserUseCase;
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

    #[Route('/user/add', name: 'app_user_web')]
    public function createFromForm(Request $request): Response
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userUseCase->create($data);

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user_web/createUserBootstrap.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user', name: 'app_user_list')]
    public function getList(): Response
    {
        $users = $this->userUseCase->findAll();

        return $this->render('user_web/getListUserBootstrap.html.twig', [
            'users' => $users,
        ]);
    }

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
