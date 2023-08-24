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

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $roles = $request->request->get('roles');
            $this->userUseCase->create($name, $roles);

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

//    #[Route('/user/{user}', name: 'app_user_update')]
//    public function update(Request $request, User $user): Response
//    {
//        $user = $this->userUseCase->find($id);
//
//        if (!$user) {
//            throw $this->createNotFoundException('Пользователь не найден');
//        }
//
//        $form = $this->createForm(UserFormType::class, $user);
//        $form->handleRequest($request);
//
//        if ($request->isMethod('POST')) {
//            $this->userUseCase->update( $id,
//                $form->get('name')->getData(),
//                $form->get('roles')->getData());
//
//            return $this->redirectToRoute('app_user_list');
//        }
//
//        return $this->render('user_web/updateUserBootstrap.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
}
