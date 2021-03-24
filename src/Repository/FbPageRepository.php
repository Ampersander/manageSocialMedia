<?php

namespace App\Repository;

use App\Entity\FbPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FbPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbPage[]    findAll()
 * @method FbPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FbPage::class);
    }

    // /**
    //  * @return FbPage[] Returns an array of FbPage objects
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
    public function findOneBySomeField($value): ?FbPage
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
