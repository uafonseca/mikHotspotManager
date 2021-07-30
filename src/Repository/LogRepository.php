<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function getLastLogs()
    {
        return $this->createQueryBuilder('l')
            // ->andWhere('YEAR(l.time) = :y AND MONTH(l.time) = :m AND DAY(l.time) =:d')
            // ->setParameters([
            //     'y' =>  date('Y'),
            //     'd' => date('d'),
            //     'm' => date('m')
            // ])
            ->orderBy('l.time', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findReverse(array $query = [])
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.time', 'DESC');

            if(count($query) > 0){
                foreach($query as $key => $value){
                    if($key === 'user'){
                        $qb->join('l.user', 'u')
                            ->andWhere('u.username =:user')
                            ->setParameter('user',$value)
                            ;
                    }else{
                        $qb
                        ->andWhere('l.'.$key.' ='.$value);
                    }
                }
            }

        return $qb ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Log[] Returns an array of Log objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Log
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
