<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findUserSubscriptions(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.startDate', 's.nextPaymentDate', 's.commitmentEndDate', 's.isActive')
            ->addSelect('t.name AS typeName', 't.price AS typePrice', 't.billingIntervalDays', 't.commitmentMonths')
            ->join('s.subscriptionType', 't')
            ->where('s.user = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('s.startDate', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findDueSubscriptions(\DateTime $today): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = true')
            ->andWhere('s.nextPaymentDate <= :today')
            ->setParameter('today', $today)
            ->join('s.subscriptionType', 't')
            ->getQuery()
            ->getResult();
    }


}
