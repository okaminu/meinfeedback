<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;

class AsSingleScalar implements SpecificationInterface, AsInterface
{
    private $parent;
    private $function;
    private $column;

    /**
     * Select Single Scalar with function constructor
     *
     * @param SpecificationInterface $parent
     * @param string $function
     * @param string $column to apply mysql function
     */
    public function __construct(SpecificationInterface $parent, $function = 'count', $column = 'id')
    {
        $this->parent = $parent;
        $this->function = $function;
        $this->column = $column;
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
        return $qb->select($this->function . '(' . $dqlAlias . '.' . $this->column .')');
    }
}
