<?php

namespace App\Repository;

use App\Entity\FbPageAndInsta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FbPageAndInsta|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbPageAndInsta|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbPageAndInsta[]    findAll()
 * @method FbPageAndInsta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbPageAndInstaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FbPageAndInsta::class);
    }

    // /**
    //  * @return FbPageAndInsta[] Returns an array of FbPageAndInsta objects
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
    public function findOneBySomeField($value): ?FbPageAndInsta
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
