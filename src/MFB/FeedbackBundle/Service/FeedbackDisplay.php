<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\RatingBundle\Entity\RatingSummary;
use MFB\FeedbackBundle\Entity\FeedbackSummary;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;

class FeedbackDisplay
{
    private $entityManager;

    private $feedbackOrder;
    
    private $ratingBounds;

    public function __construct(EntityManager $em, $feedbackOrder, $ratingBoundaries)
    {
        $this->entityManager = $em;
        $this->feedbackOrder = $feedbackOrder;
        $this->ratingBounds = $ratingBoundaries;
    }

    public function getChannelFeedbackCount($channelId)
    {
        $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelFeedbackCount($channelId);
        return $feedbackCount;
    }

    public function getFeedbackSummaryList($channelId)
    {
        $feedbackList = $this->getFeedbackList($channelId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getActiveFeedbackSummaryList($channelId)
    {
        $feedbackList = $this->getActiveFeedbackList($channelId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getFeedbackList($channelId, $criteria = array())
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array_merge(array('channelId' => $channelId), $criteria),
                $this->feedbackOrder
            );
        return $feedbackList;
    }

    public function getActiveFeedbackList($channelId)
    {
        return $this->getFeedbackList($channelId, array('isEnabled' =>  1));
    }

    /**
     * @param $channelId
     * @return array
     */
    public function createChannelRatingSummary($channelId)
    {
        $ratings = array();
        $ratings[] = new RatingSummary('Overall', $this->getChannelRatingAverage($channelId));

        $channelCriteriaRatings = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelCriteriaRatings($channelId, $this->ratingBounds['min'], $this->ratingBounds['max']);

        foreach ($channelCriteriaRatings as $singleCriteria) {
            $ratings[] = new RatingSummary($singleCriteria['name'], $this->roundHalfUp($singleCriteria['rating']));
        }
        return $ratings;
    }

    public function getChannelRatingAverage($channelId)
    {
        $ratingAverage = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelRatingAverage($channelId, $this->ratingBounds['min'], $this->ratingBounds['max']);

        return $this->roundHalfUp($ratingAverage);
    }


    private function getFeedbackRatingAverage($feedbackId)
    {
        $ratingAverage = $this->entityManager->getRepository("MFBFeedbackBundle:Feedback")
            ->getFeedbackRatingAverage($feedbackId);

        return $this->roundHalfUp($ratingAverage);
    }

    private function roundHalfUp($number)
    {
        return round($number, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * @param $feedbackList
     * @return array
     */
    private function createFeedbackSummaryList($feedbackList)
    {
        $feedbackSummaryList = array();
        foreach ($feedbackList as $feedback) {
            $feedbackSummaryList[] = $this->createFeedbackSummaryItem($feedback);
        }
        return $feedbackSummaryList;
    }

    /**
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return FeedbackSummary
     */
    private function createFeedbackSummaryItem($feedback)
    {
        $singleSummary = new FeedbackSummary();
        $singleSummary = $this->addServiceSummary($singleSummary, $feedback->getService());
        $singleSummary->setRatings($this->createFeedbackRatingSummary($feedback));
        $singleSummary->setFeedback($feedback);
        return $singleSummary;
    }

    /**
     * @param FeedbackSummary $singleSummary
     * @param ServiceEntity $service
     * @return FeedbackSummary
     */
    private function addServiceSummary(FeedbackSummary $singleSummary, ServiceEntity $service)
    {
        $serviceGroup = $service->getServiceGroup();
        $serviceProvider = $service->getServiceProvider();
        $singleSummary->setServiceTypeName($serviceGroup->getName());
        $serviceProviderInfo = $serviceProvider->getFirstname() . ' ' . $serviceProvider->getLastname();
        $singleSummary->setServiceProviderInfo($serviceProviderInfo);
        return $singleSummary;
    }

    /**
     * @param $feedback
     * @return array
     */
    private function createFeedbackRatingSummary(FeedbackEntity $feedback)
    {
        $ratings = array();
        $ratings[] = new RatingSummary('Overall', $this->getFeedbackRatingAverage($feedback->getId()));

        foreach ($feedback->getFeedbackRating() as $criteria) {
            $criteriaName = $criteria->getRatingCriteria()->getRatingCriteria()->getName();
            $ratings[] = new RatingSummary($criteriaName, $criteria->getRating());
        }
        return $ratings;
    }
}