<?php

namespace App\Repository;

use App\Entity\TemplatePost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplatePost|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplatePost|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplatePost[]    findAll()
 * @method TemplatePost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplatePostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplatePost::class);
    }

    // /**
    //  * @return TemplatePost[] Returns an array of TemplatePost objects
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
 /**
      * @return TemplatePost[] Returns an array of TemplatePost objects
        */
    
        public function findByUser($user)
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.user = :user')
                ->setParameter('user', $user)
                ->orderBy('s.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }
    /*
    public function findOneBySomeField($value): ?TemplatePost
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
