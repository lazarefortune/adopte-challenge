<?php

namespace App\Command;

use App\Entity\Purchase;
use App\Repository\SubscriptionRepository;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:subscriptions:renew',
    description: 'Renouvelle les abonnements arrivés à échéance',
)]
class SubscriptionsRenewCommand extends Command
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepo,
        private readonly PaymentService         $payment,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new \DateTime('today');

        $subscriptions = $this->subscriptionRepo->findDueSubscriptions($today);

        foreach ($subscriptions as $sub) {

            $user = $sub->getUser();
            $type = $sub->getSubscriptionType();

            if ($sub->getCommitmentEndDate() < $today && !$type->isAutoRenew()) {
                $sub->setIsActive(false);
                continue;
            }

            $transaction = $this->payment->createTransaction(
                $user->getRemotePaymentId(),
                $type->getPrice()
            );

            if (!$transaction || !isset($transaction['data'])) {
                $sub->setIsActive(false);
                continue;
            }

            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setSubscription($sub);
            $purchase->setAmount($type->getPrice());
            $purchase->setTransactionId($transaction['data']);
            $purchase->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($purchase);

            $next = (clone $sub->getNextPaymentDate())
                ->modify("+" . $type->getBillingIntervalDays() . " days");

            $sub->setNextPaymentDate($next);
        }

        $this->em->flush();

        $output->writeln("Renouvellements effectués.");

        return Command::SUCCESS;
    }
}
