<?php

namespace App\Controller;

use App\Enum\ApiGroupsEnum;
use App\Exception\EntityNotFoundException;
use App\Model\UserForm\UserFormType;
use App\Service\User\UserUseCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserWebController extends AbstractController
{
    const CACHE_KEY = 'search_user_list_cache';
    const EXPIREDTIME = 3600;

    public function __construct(
        private UserUseCase $userUseCase,
        private CacheInterface $cache,
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
     * @throws InvalidArgumentException
     */
    #[Route('/user', name: 'app_user_list')]
    public function getList(): Response
    {
        $cachedUsers = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIREDTIME);

            return $this->userUseCase->findAll();
        });

        return $this->render('user_web/getListUserBootstrap.html.twig', [
            'users' => $cachedUsers,
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
