<?php

namespace App\Service;

use App\Entity\Purchase;
use App\Entity\Subscription;
use App\Entity\SubscriptionType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class SubscriptionManager
{
    public function __construct(
        private PaymentService         $paymentService,
        private EntityManagerInterface $em
    ) {}

    public function subscribe(User $user, SubscriptionType $type, string $cardNumber, int $cvv): bool
    {
        $remoteId = $this->paymentService->ensureRemoteUser($user, $cardNumber, $cvv);
        if (!$remoteId) {
            return false;
        }

        $transaction = $this->paymentService->createTransaction($remoteId, $type->getPrice());
        if (!$transaction || !isset($transaction['data'])) {
            return false;
        }

        $sub = new Subscription();
        $sub->setUser($user);

        $now = new \DateTime();
        $sub->setSubscriptionType($type);
        $sub->setStartDate($now);
        $sub->setNextPaymentDate((clone $now)->modify("+{$type->getBillingIntervalDays()} days"));
        $sub->setCommitmentEndDate((clone $now)->modify("+{$type->getCommitmentMonths()} months"));
        $sub->setIsActive(true);

        $this->em->persist($sub);


        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setSubscription($sub);
        $purchase->setAmount($type->getPrice());
        $purchase->setTransactionId($transaction['data']);
        $purchase->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($purchase);

        $this->em->flush();

        return true;
    }
}
