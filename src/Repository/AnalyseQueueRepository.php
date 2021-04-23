<?php

namespace App\Repository;

use App\Entity\AnalyseQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalyseQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyseQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyseQueue[]    findAll()
 * @method AnalyseQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyseQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyseQueue::class);
    }

    // /**
    //  * @return AnalyseQueue[] Returns an array of AnalyseQueue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnalyseQueue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
