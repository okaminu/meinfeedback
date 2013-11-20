<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;

use \Doctrine\ORM\Query;

class LimitLastN implements SpecificationInterface, OrderInterface
{
    private $parent;

    protected $limit;

    public function __construct(SpecificationInterface $parent, $limit = 4)
    {
        $this->parent = $parent;

        $this->limit = $limit;
    }

    public function modifyQuery(Query $query)
    {
        $query->setMaxResults($this->limit);
    }

    public function match(QueryBuilder $qb, $dqlAlias)
    {
        return $this->parent->match($qb, $dqlAlias);
    }

    public function orderBy(QueryBuilder $qb, $dqlAlias)
    {
        $qb->orderBy($dqlAlias.'.id', 'desc');
    }


}