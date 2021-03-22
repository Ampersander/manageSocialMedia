<?php

namespace App\Repository;

use App\Entity\TwitterAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TwitterAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterAccount[]    findAll()
 * @method TwitterAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterAccount::class);
    }

    // /**
    //  * @return TwitterAccount[] Returns an array of TwitterAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TwitterAccount
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
