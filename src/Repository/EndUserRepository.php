<?php

namespace App\Repository;

use App\Entity\EndUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EndUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method EndUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method EndUser[]    findAll()
 * @method EndUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EndUser::class);
    }

    // /**
    //  * @return EndUser[] Returns an array of EndUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EndUser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
