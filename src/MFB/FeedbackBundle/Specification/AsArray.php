<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;

use \Doctrine\ORM\Query;

class AsArray implements SpecificationInterface, AsInterface
{
    private $parent;

    public function __construct(SpecificationInterface $parent)
    {
        $this->parent = $parent;
    }

    public function modifyQuery(Query $query)
    {
        $query->setHydrationMode(Query::HYDRATE_ARRAY);
    }

    public function match(QueryBuilder $qb, $dqlAlias)
    {
        return $this->parent->match($qb, $dqlAlias);
    }

    public function select(QueryBuilder $qb, $dqAlias)
    {

    }

    public function supports($className)
    {
        return true;
    }

}