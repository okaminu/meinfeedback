<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use MFB\FeedbackBundle\Entity\Feedback;

class FilterAccountId implements SpecificationInterface
{
    private $accountId;

    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }

    public function match(QueryBuilder $qb, $dqlAlias)
    {
        $qb->setParameter('accountId', $this->accountId);

        return $qb->expr()->eq($dqlAlias . '.accountId', ':accountId');
    }

    public function modifyQuery(Query $query) { /* empty ***/ }

    public function supports($className)
    {
        return ($className instanceof Feedback);
    }
}