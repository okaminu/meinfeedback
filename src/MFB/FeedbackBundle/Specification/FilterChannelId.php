<?php

namespace MFB\FeedbackBundle\Specification;

use \Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query;
use MFB\FeedbackBundle\Entity\Feedback;

class FilterChannelId implements SpecificationInterface
{
    private $channelId;

    public function __construct($channelId)
    {
        $this->channelId = $channelId;
    }

    public function match(QueryBuilder $qb, $dqlAlias)
    {
        $qb->setParameter('channelId', $this->channelId);

        return $qb->expr()->eq($dqlAlias . '.channelId', ':channelId');
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