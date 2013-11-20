<?php


namespace MFB\FeedbackBundle\Repository;

use MFB\FeedbackBundle\Specification\SpecificationInterface;

interface FeedbackRepositoryInterface
{
    /**
     * @param SpecificationInterface $criteria
     * @return array<User>|array<array>
     ***/
    public function match(SpecificationInterface $criteria);
}
