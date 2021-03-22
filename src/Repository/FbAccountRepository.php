<?php

namespace App\Repository;

use App\Entity\FbAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FbAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbAccount[]    findAll()
 * @method FbAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FbAccount::class);
    }

    // /**
    //  * @return FbAccount[] Returns an array of FbAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FbAccount
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
