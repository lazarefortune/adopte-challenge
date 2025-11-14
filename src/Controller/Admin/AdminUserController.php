<?php

namespace App\Controller\Admin;

use App\Service\UsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/wp-admin', name: 'admin_')]
class AdminUserController extends AbstractController
{

    #[Route('/utilisateurs', name: 'users')]
    public function index(UsersService $usersService): Response
    {
        $users = $usersService->getUsersForAdmin();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/users/{id}', name: 'user_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, UsersService $usersService): Response
    {
        $data = $usersService->getUserDetails($id);

        if (!$data) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        return $this->render('admin/users/detail.html.twig', $data);
    }
}
