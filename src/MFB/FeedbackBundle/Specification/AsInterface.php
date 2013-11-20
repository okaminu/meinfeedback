<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;

interface AsInterface
{
    public function select(QueryBuilder $qb, $dqlAlias);
}