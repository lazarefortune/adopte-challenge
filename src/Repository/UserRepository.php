<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAllForAdminList(): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id', 'u.email', 'u.name', 'u.remotePaymentId')
            ->addSelect('(SELECT COUNT(s.id) FROM App\Entity\Subscription s WHERE s.user = u.id) AS subscriptionsCount')
            ->addSelect('(SELECT COUNT(p.id) FROM App\Entity\Purchase p WHERE p.user = u.id) AS purchasesCount')
            ->where('u.roles LIKE :client')
            ->andWhere('u.roles NOT LIKE :admin')
            ->setParameter('client', '%ROLE_CLIENT%')
            ->setParameter('admin', '%ROLE_ADMIN%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findUserInfo(int $id): ?array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id', 'u.email', 'u.name', 'u.remotePaymentId')
            ->addSelect('(SELECT COUNT(s.id) FROM App\Entity\Subscription s WHERE s.user = u.id) AS subscriptionsCount')
            ->addSelect('(SELECT COUNT(p.id) FROM App\Entity\Purchase p WHERE p.user = u.id) AS purchasesCount')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
