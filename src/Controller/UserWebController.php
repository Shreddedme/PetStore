<?php

namespace App\Controller;

use App\User\UserUseCase;
use App\UserForm\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $roles = $request->request->get('roles');
            $this->userUseCase->create($name, $roles);

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user_web/createUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user', name: 'app_user_list')]
    public function getList(): Response
    {
        $users = $this->userUseCase->findAll();

        return $this->render('user_web/getListUser.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_update')]
    public function update(Request $request, int $id): Response
    {
        $user = $this->userUseCase->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $this->userUseCase->update( $id,
                $form->get('name')->getData(),
                $form->get('roles')->getData());

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user_web/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
