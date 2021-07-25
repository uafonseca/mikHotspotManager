<?php

namespace App\Repository;

use App\Entity\Investmenst;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Investmenst|null find($id, $lockMode = null, $lockVersion = null)
 * @method Investmenst|null findOneBy(array $criteria, array $orderBy = null)
 * @method Investmenst[]    findAll()
 * @method Investmenst[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvestmenstRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investmenst::class);
    }

    /**
     * @return Investmenst[] Returns an array of Investmenst objects
     */

    public function investmentThisMonth(User $user)
    {
        return $this->createQueryBuilder('i')
            ->select('sum(i.mount)')
            ->andWhere('MONTH(i.createdAt) = :m')
            ->andWhere('YEAR(i.createdAt) = :y')
            ->setParameter('m', date('m'))
            ->setParameter('y', date('Y'))
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Investmenst
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
