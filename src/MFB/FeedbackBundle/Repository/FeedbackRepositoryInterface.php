<?php


namespace MFB\FeedbackBundle\Repository;

interface FeedbackRepositoryInterface
{
    /**
     * @param FeedbackSpecification $criteria
     * @return array<User>|array<array>
     ***/
    public function match(FeedbackSpecification $criteria);
}
