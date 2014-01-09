<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\Entity\FeedbackSummaryCriteria;
use MFB\FeedbackBundle\Entity\FeedbackSummary;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FeedbackDisplay
{
    private $entityManager;

    private $feedbackOrder;

    public function __construct(EntityManager $em, $feedbackOrder)
    {
        $this->entityManager = $em;
        $this->feedbackOrder = $feedbackOrder;
    }


    public function getFeedbackCount($accountId)
    {
        $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getAccountFeedbackCount($accountId);
        return $feedbackCount;
    }

    public function getChannelRatingAverage($accountId)
    {
        $ratingAverage = $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelRatingAverage($accountId);

        return $this->roundHalfUp($ratingAverage);
    }

    public function getFeedbackSummaryList($accountId)
    {
        $feedbackList = $this->getFeedbackList($accountId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getActiveFeedbackSummaryList($accountId)
    {
        $feedbackList = $this->getActiveFeedbackList($accountId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getFeedbackList($accountId)
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array('accountId' => $accountId),
                $this->feedbackOrder
            );

        return $feedbackList;
    }

    public function getActiveFeedbackList($accountId)
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array('accountId' => $accountId, 'isEnabled' =>  1),
                $this->feedbackOrder
            );

        return $feedbackList;
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
        $singleSummary->setRatings($this->createRatingSummary($feedback));
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
    private function createRatingSummary(FeedbackEntity $feedback)
    {
        $ratings = array();
        $ratings[] = new FeedbackSummaryCriteria(
            'Overall',
            $this->getFeedbackRatingAverage($feedback->getId())
        );

        foreach ($feedback->getFeedbackRating() as $criteria) {
            $criteriaName = $criteria->getRatingCriteria()->getRatingCriteria()->getName();
            $ratings[] = new FeedbackSummaryCriteria($criteriaName, $criteria->getRating());
        }
        return $ratings;
    }

    /**
     * @param $feedbackId
     * @return float
     */
    private function getFeedbackRatingAverage($feedbackId)
    {
        $ratingAverage = $this->entityManager
            ->getRepository("MFBFeedbackBundle:Feedback")->getFeedbackRatingAverage($feedbackId);
        return $this->roundHalfUp($ratingAverage);
    }

}