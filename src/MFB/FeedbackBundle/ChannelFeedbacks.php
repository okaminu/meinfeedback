<?php
namespace MFB\FeedbackBundle;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use MFB\FeedbackBundle\Entity\FeedbackSummaryPage;
use MFB\RatingBundle\Entity\RatingSummary;
use MFB\FeedbackBundle\Entity\FeedbackSummaryItem;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;

class ChannelFeedbacks
{
    private $entityManager;

    private $feedbackOrder;

    private $ratingBounds;

    private $elementsPerPage;

    private $channelId;

    public function __construct(EntityManager $em, $feedbackOrder, $ratingBoundaries, $feedbacksPerPage, $channelId)
    {
        $this->entityManager = $em;
        $this->feedbackOrder = $feedbackOrder;
        $this->ratingBounds = $ratingBoundaries;
        $this->elementsPerPage = $feedbacksPerPage;
        $this->channelId = $channelId;
    }

    /**
     * @param mixed $elementsPerPage
     */
    public function setElementsPerPage($elementsPerPage)
    {
        $this->elementsPerPage = $elementsPerPage;
    }

    /**
     * @param mixed $channelId
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }

    public function getChannelFeedbackCount()
    {
        $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelFeedbackCount($this->channelId);
        return $feedbackCount;
    }

    public function getFeedbackSummary($page = 1)
    {
        $qb = $this->getFeedbackQueryBuilder($this->channelId);
        return $this->createFeedbackSummaryPage($this->getPage($qb, $page));
    }

    public function getActiveFeedbackSummary($page = 1)
    {
        $qb = $this->getFeedbackQueryBuilder($this->channelId);
        $qb->andWhere($qb->expr()->eq('feedback.isEnabled', 1));
        return $this->createFeedbackSummaryPage($this->getPage($qb, $page));
    }

    /**
     * @return array
     */
    public function createChannelRatingSummary()
    {
        $ratings = array();
        $ratings[] = new RatingSummary('Overall', $this->getChannelRatingAverage($this->channelId));

        $channelCriteriaRatings = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelCriteriaRatings($this->channelId, $this->ratingBounds['min'], $this->ratingBounds['max']);

        foreach ($channelCriteriaRatings as $singleCriteria) {
            $ratings[] = new RatingSummary($singleCriteria['name'], $this->roundHalfUp($singleCriteria['rating']));
        }
        return $ratings;
    }

    public function getChannelRatingAverage()
    {
        $ratingAverage = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelRatingAverage($this->channelId, $this->ratingBounds['min'], $this->ratingBounds['max']);

        return $this->roundHalfUp($ratingAverage);
    }

    private function getFeedbackQueryBuilder()
    {
        return $this->entityManager
            ->getRepository("MFBFeedbackBundle:Feedback")
            ->getFeedbackQueryBuilder($this->channelId, $this->feedbackOrder);
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
     * @param PaginationInterface $page
     * @return FeedbackSummaryPage
     */
    private function createFeedbackSummaryPage(PaginationInterface $page)
    {
        $lastPageNumber = ceil($page->getTotalItemCount() / $page->getItemNumberPerPage());
        $feedbackSummaryPage = new FeedbackSummaryPage($page->getCurrentPageNumber(), $lastPageNumber);
        foreach ($page as $feedback) {
            $feedbackSummaryPage->addItem($this->createFeedbackSummaryItem($feedback));
        }
        return $feedbackSummaryPage;
    }

    /**
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return FeedbackSummaryItem
     */
    private function createFeedbackSummaryItem($feedback)
    {
        $singleSummary = new FeedbackSummaryItem();
        $singleSummary = $this->addServiceSummary($singleSummary, $feedback->getService());
        $singleSummary->setRatings($this->createFeedbackRatingSummary($feedback));
        $singleSummary->setFeedback($feedback);
        return $singleSummary;
    }

    /**
     * @param FeedbackSummaryItem $singleSummary
     * @param ServiceEntity $service
     * @return FeedbackSummaryItem
     */
    private function addServiceSummary(FeedbackSummaryItem $singleSummary, ServiceEntity $service)
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

    /**
     * @param $list
     * @param $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    private function getPage($list, $page)
    {
        $paginator = new Paginator();
        return $paginator->paginate($list, $page, $this->elementsPerPage);
    }
}