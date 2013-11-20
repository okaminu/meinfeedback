<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;

class FilterWithRating implements SpecificationInterface
{
    public function match(QueryBuilder $qb, $dqlAlias)
    {
        return $qb->expr()->isNotNull($dqlAlias . '.rating');
    }

    public function modifyQuery(Query $query)
    {
        /* empty ***/
    }
}