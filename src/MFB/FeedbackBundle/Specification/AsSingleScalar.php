<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;

class AsSingleScalar implements SpecificationInterface, AsInterface
{
    private $parent;
    private $type;

    public function __construct(SpecificationInterface $parent, $type = 'count')
    {
        $this->parent = $parent;
        $this->type = $type;
    }

    public function modifyQuery(Query $query)
    {
        $query->setHydrationMode(Query::HYDRATE_SINGLE_SCALAR);
    }

    public function match(QueryBuilder $qb, $dqlAlias)
    {
        return $this->parent->match($qb, $dqlAlias);
    }

    public function select(QueryBuilder $qb, $dqlAlias)
    {
        return $qb->select($this->type . '(' . $dqlAlias . '.id)');
    }
}
