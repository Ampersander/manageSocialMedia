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

/*
    public function findByUser($user)
    {
        return $this->createQueryBuilder('e')
        ->addSelect('r') 
        ->leftJoin('e.socialMediaAccount', 'r')
        ->where('r.user = :parameter')
        ->setParameter('parameter', $user)
        ->getQuery();
        ;
    }*/

    public function findByUser($user)
    {
        return $this->createQueryBuilder('od')
        ->join('od.socialMediaAccount', 'o')
        ->addSelect('o')
        ->where('o.user = :user')
        ->setParameter('user', $user)
        ->getQuery()->getResult();
    }
  

/**
     * @return FbAccount Return FbAccount objects
     */
    
    public function findById($id)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

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
