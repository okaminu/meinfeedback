<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;

interface SpecificationInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $dqlAlias
     *
     * @return \Doctrine\ORM\Query\Expr
     ***/
    public function match(QueryBuilder $qb, $dqlAlias);

    /**
     * @param \Doctrine\ORM\Query $query
     ***/
    public function modifyQuery(Query $query);

    /**
     * Check if specification supports given entity
     * @param string $className
     * @return bool
     ***/
    public function supports($className);
}