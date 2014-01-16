<?php
namespace MFB\FeedbackBundle;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use MFB\FeedbackBundle\Summary\Feedback as FeedbackSummary;

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
        return $this->createFeedbackSummaryPage($this->makePaginator($qb, $page));
    }

    public function getActiveFeedbackSummary($page = 1)
    {
        $qb = $this->getFeedbackQueryBuilder($this->channelId);
        $qb->andWhere($qb->expr()->eq('feedback.isEnabled', 1));
        return $this->createFeedbackSummaryPage($this->makePaginator($qb, $page));
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

    public function getFeedbackRatingAverage($feedbackId)
    {
        $ratingAverage = $this->entityManager->getRepository("MFBFeedbackBundle:Feedback")
            ->getFeedbackRatingAverage($feedbackId);

        return $this->roundHalfUp($ratingAverage);
    }

    public function getChannelRatingSummary()
    {
        $feedbackSummary = new FeedbackSummary($this);
        $channelRatings = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getChannelCriteriaRatings($this->channelId, $this->ratingBounds['min'], $this->ratingBounds['max']);

        return $feedbackSummary->createChannelRatingSummary($channelRatings, $this->getChannelRatingAverage());
    }

    private function createFeedbackSummaryPage(PaginationInterface $makePaginator)
    {
        $feedbackSummary = new FeedbackSummary($this);
        return $feedbackSummary->createFeedbackPage($makePaginator);
    }

    public function roundHalfUp($number)
    {
        return round($number, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * @param $list
     * @param $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    private function makePaginator($list, $page)
    {
        $paginator = new Paginator();
        return $paginator->paginate($list, $page, $this->elementsPerPage);
    }
}