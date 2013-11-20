<?php
namespace MFB\FeedbackBundle\Specification;

use Doctrine\ORM\QueryBuilder;

interface OrderInterface
{
    public function orderBy(QueryBuilder $qb, $dqlAlias);
}
