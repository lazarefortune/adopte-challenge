<?php

namespace App\Controller;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Form\SubscriptionPaymentType;
use App\Service\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SubscribeController extends AbstractController
{

    #[Route('/abonnement', name: 'app_subscribe')]
    #[IsGranted('ROLE_CLIENT')]
    public function index( Request $request, SubscriptionManager $subscriptionManager ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!empty($user->getSubscriptions()->toArray())) {
            $this->addFlash('success', "Vous avez déjà un abonnement en cours, merci !");
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(SubscriptionPaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var SubscriptionType $type */
            $type = $data['subscription_type'];

            $ok = $subscriptionManager->subscribe(
                $user,
                $type,
                $data['card_number'],
                $data['cvv']
            );

            if (!$ok) {
                $this->addFlash('error', 'Impossible de finaliser la souscription.');
                return $this->redirectToRoute('app_subscribe');
            }

            $this->addFlash('success', 'Votre abonnement a bien été activé !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('subscribe/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
