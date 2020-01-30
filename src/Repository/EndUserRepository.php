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
class EndUserRepository extends AbstractRepository
{
    public function search($reseller, $term, $order = null, $limit = null, $page = null)
    {
        $order = $order?? "asc";
        $limit = $limit?? 20;
        $page = $page?? 1;

        $qb = $this
            ->createQueryBuilder('e')
            ->select('e')
            ->where('e.reseller = :id')
            ->setParameter('id', $reseller->getId())
            ->orderBy('e.lastName', $order)
        ;

        if ($term) {
            $qb
                ->andwhere('e.lastName LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $page);
    }

    public function stringValExistsForOtherId($fieldName, $email, $id)
    {
        $result = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.'.$fieldName.' LIKE ?1')
            ->andwhere('e.id != '.$id)
            ->setParameter(1, $email)
            ->getQuery()
            ->getResult();

        return $result;
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
