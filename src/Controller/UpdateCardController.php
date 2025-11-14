<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateCardType;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UpdateCardController extends AbstractController
{
    #[Route('/parametres', name: 'app_settings')]
    #[IsGranted('ROLE_CLIENT')]
    public function update(
        Request $request,
        PaymentService $payment,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getRemotePaymentId()) {
            $this->addFlash('error', 'Aucun moyen de paiement à mettre à jour.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(UpdateCardType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $res = $payment->updateRemoteUser(
                $user->getRemotePaymentId(),
                $data['card_number'],
                $data['cvv']
            );

            if (!$res) {
                $this->addFlash('error', 'La mise à jour du moyen de paiement a échoué.');
                return $this->redirectToRoute('app_settings');
            }

            $this->addFlash('success', 'Votre carte a bien été mise à jour !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('payment/update_card.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
