<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use MFB\FeedbackBundle\Entity\Feedback;

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

    public function supports($className)
    {
        return ($className instanceof Feedback);
    }
}