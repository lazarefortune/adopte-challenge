<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function findAllForAdminList(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.amount', 'p.transactionId', 'p.createdAt')
            ->addSelect('u.email AS userEmail')
            ->addSelect('t.name AS subscriptionName')
            ->join('p.user', 'u')
            ->leftJoin('p.subscription', 's')
            ->leftJoin('s.subscriptionType', 't')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findUserPurchases(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id', 'p.amount', 'p.transactionId', 'p.createdAt')
            ->addSelect('t.name AS subscriptionName')
            ->leftJoin('p.subscription', 's')
            ->leftJoin('s.subscriptionType', 't')
            ->where('p.user = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }


}
