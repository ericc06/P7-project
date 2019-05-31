<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    //protected function paginate(QueryBuilder $qb, $limit = 20, $offset = 0)
    protected function paginate(QueryBuilder $qb, $limit = 20, $page = 1)
    {
        if (!(0 < $limit && 0 < $page)) {
            throw new \LogicException('$limit and $page must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));

        $pager->setMaxPerPage((int) $limit);

        if ($page > $pager->getNbPages()) {
            throw new \OutOfRangeException('The requested page number is out of range.');
        }

        try {
            $pager->setCurrentPage($page);
        } catch (Exception $e) {
            echo 'Exception : ',  $e->getMessage(), "\n";
        }

        return $pager;
    }
}
