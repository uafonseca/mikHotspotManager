<?php

namespace App\Repository;

use App\Entity\Router;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Router|null find($id, $lockMode = null, $lockVersion = null)
 * @method Router|null findOneBy(array $criteria, array $orderBy = null)
 * @method Router[]    findAll()
 * @method Router[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RouterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Router::class);
    }

    // /**
    //  * @return Router[] Returns an array of Router objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Router
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
