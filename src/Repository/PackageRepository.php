<?php

namespace App\Repository;

use App\Entity\Package;
use App\Entity\User;
use DateTime;
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
     * Undocumented function
     *
     * @param User $user
     * @param boolean $check
     * @return void
     */
    public function createdToday(User $user, bool $check = true)
    {
        $qb = $this->createQueryBuilder('p')

            ->andWhere('MONTH(p.createdAt) = :m')
            ->andWhere('YEAR(p.createdAt) = :y AND DAY(p.createdAt) =:d')
            ->setParameter('m', date('m'))
            ->setParameter('y', date('Y'))
            ->setParameter('d', date('d'));
        if ($check) {
            $qb
                ->select('sum(p.price)')
                ->andWhere('p.createdBy = :user')
                ->setParameter('user', $user);
        } else {
            $qb
                ->select('sum(p.price)')
                ->join('p.createdBy', 'user')
                ->andWhere('p.createdBy <> :user')
                ->setParameter('user', $user);
        }
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function allToday(User $user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('MONTH(p.createdAt) = :m')
            ->andWhere('YEAR(p.createdAt) = :y AND DAY(p.createdAt) =:d')
            ->andWhere('p.createdBy = :user')
            ->setParameter('user', $user)
            ->setParameter('m', date('m'))
            ->setParameter('y', date('Y'))
            ->setParameter('d', date('d'))
            ->getQuery()
            ->getResult();
    }


    public function findByFilters(array $filters = [])
    {
        $qb = $this->createQueryBuilder('p');
        if (isset($filters['user']) && $filters['user']) {
            $qb
                ->andWhere('p.user =:id')
                ->setParameter('id', $filters['user']);
        }
        if (isset($filters['fecha']) && $filters['fecha']) {

            $date = new DateTime($filters['fecha']);
            $qb
                ->andWhere('MONTH(p.createdAt) = :m')
                ->andWhere('YEAR(p.createdAt) = :y AND DAY(p.createdAt) =:d')
                ->setParameter('m',  $date->format('m'))
                ->setParameter('d', $date->format('d'))
                ->setParameter('y', $date->format('Y'));
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @param boolean $check
     * @return void
     */
    public function createdThisMonth(User $user, bool $check = true)
    {
        $qb = $this->createQueryBuilder('p')

            ->andWhere('MONTH(p.createdAt) = :m')
            ->andWhere('YEAR(p.createdAt) = :y')
            ->setParameter('m', date('m'))
            ->setParameter('y', date('Y'));
        if ($check) {
            $qb
                ->select('sum(p.price)')
                ->andWhere('p.createdBy = :user')
                ->setParameter('user', $user);
        } else {
            $qb
                ->select('sum(p.price)')
                ->andWhere('p.createdBy <> :user')
                ->setParameter('user', $user);
        }
        return $qb->getQuery()
            ->getResult();
    }

    public function myDebts(User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.debt =:t')
            ->setParameter('t', true)
            ->andWhere('p.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     * @param string $username
     * @return void
     */
    public function findByUsername(User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('sum(d.price) as deuda')
            ->join('d.user', 'user')
            ->where('user =:user')
            ->andWhere('d.debt =:t')
            ->setParameter('t', true)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
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
