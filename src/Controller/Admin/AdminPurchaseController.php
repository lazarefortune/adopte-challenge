<?php

namespace App\Controller\Admin;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/wp-admin', name: 'admin_')]
class AdminPurchaseController extends AbstractController
{
    #[Route('/achats', name: 'purchases')]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        $purchases = $purchaseRepository->findAllForAdminList();

        return $this->render('admin/purchases/index.html.twig', [
            'purchases' => $purchases
        ]);
    }
}
