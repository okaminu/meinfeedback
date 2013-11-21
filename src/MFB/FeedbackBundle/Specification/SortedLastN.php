<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;

use \Doctrine\ORM\Query;

class SortedLastN implements SpecificationInterface, OrderInterface
{
    private $parent;

    protected $limit;

    private $sortColumn;

    private $sortDirection;

    public function __construct(
        SpecificationInterface $parent,
        $sortColumn = 'sort',
        $sortDirection = 'ASC',
        $limit = 4
    ) {
        $this->parent = $parent;

        $this->sortColumn = $sortColumn;

        $this->sortDirection = $sortDirection;

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

        $qb->orderBy($dqlAlias.'.'. $this->sortColumn, $this->sortDirection);
    }
}