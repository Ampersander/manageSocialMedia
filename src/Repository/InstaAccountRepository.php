<?php

namespace App\Repository;

use App\Entity\InstaAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InstaAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstaAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstaAccount[]    findAll()
 * @method InstaAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstaAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstaAccount::class);
    }

    // /**
    //  * @return InstaAccount[] Returns an array of InstaAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InstaAccount
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
