<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use MFB\FeedbackBundle\Entity\Feedback;

class FilterIsEnabled implements SpecificationInterface
{


    public function match(QueryBuilder $qb, $dqlAlias)
    {
        $qb->setParameter('isEnabled', 1);

        return $qb->expr()->eq($dqlAlias . '.isEnabled', ':isEnabled');
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