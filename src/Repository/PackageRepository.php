<?php

namespace App\Repository;

use App\Entity\Package;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    /**
     * @return Package[] Returns an array of Package objects
     */

    public function createdToday(User $user)
    {
        return $this->createQueryBuilder('p')
            ->select('sum(p.price)')
            ->andWhere('p.createdBy = :user')
            ->andWhere('MONTH(p.createdAt) = :m')
            ->andWhere('YEAR(p.createdAt) = :y')
            ->andWhere('DAY(p.createdAt) = :d')
            ->setParameter('user', $user)
            ->setParameter('m', date('m'))
            ->setParameter('d', date('d'))
            ->setParameter('y', date('Y'))
            ->getQuery()
            ->getResult()
        ;
    }

    public function createdThisMonth(User $user)
    {
        return $this->createQueryBuilder('p')
            ->select('sum(p.price)')
            ->andWhere('p.createdBy = :user')
            ->andWhere('MONTH(p.createdAt) = :m')
            ->andWhere('YEAR(p.createdAt) = :y')
            ->setParameter('user', $user)
            ->setParameter('m', date('m'))
            ->setParameter('y', date('Y'))
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Package
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
