<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Entity\ChannelRatingCriteria;
use MFB\FeedbackBundle\Entity\FeedbackRating as FeedbackRatingEntity;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FeedbackRating
{
    private $entityManager;


    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewFeedbackRating(ChannelRatingCriteria $criteria, FeedbackEntity $feedbackEntity)
    {
        $feedbackRating = new FeedbackRatingEntity();
        $feedbackRating->setRatingCriteria($criteria);
        $feedbackRating->setRatingCriteriaId($criteria->getId());
        $feedbackRating->setFeedback($feedbackEntity);

        return $feedbackRating;
    }
}