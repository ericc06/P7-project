<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use App\Exception\LimitOrPageLogicException;
use App\Exception\PageOutOfRangeException;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit, $page)
    {

        if (!(filter_var($limit, FILTER_VALIDATE_INT) && 0 < $limit) &&
            !(filter_var($page, FILTER_VALIDATE_INT) && 0 < $page)
            ) {
            $message = '$limit and $page must be integers greater than 0.';

            throw new LimitOrPageLogicException($message);
        }

        if (!(filter_var($limit, FILTER_VALIDATE_INT) && 0 < $limit)) {
            $message = '$limit must be an integer greater than 0.';

            throw new LimitOrPageLogicException($message);
        }

        if (!(filter_var($page, FILTER_VALIDATE_INT) && 0 < $page)) {
            $message = '$page must be an integer greater than 0.';

            throw new LimitOrPageLogicException($message);
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));

        $pager->setMaxPerPage((int) $limit);

        if ($page > $pager->getNbPages()) {
            $message = 'The requested page number is out of range.';

            throw new PageOutOfRangeException($message);
        }

        try {
            $pager->setCurrentPage($page);
        } catch (Exception $e) {
            echo 'Exception : ',  $e->getMessage(), "\n";
        }

        return $pager;
    }
}
