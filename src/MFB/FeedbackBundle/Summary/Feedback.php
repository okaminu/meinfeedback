<?php
namespace MFB\FeedbackBundle\Summary;

use Knp\Component\Pager\Pagination\PaginationInterface;
use MFB\FeedbackBundle\ChannelFeedbacks;
use MFB\FeedbackBundle\Summary\FeedbackPage as FeedbackSummaryPage;
use MFB\FeedbackBundle\Summary\FeedbackItem as FeedbackSummaryItem;
use MFB\RatingBundle\Entity\RatingSummary;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;

class Feedback
{
    private $channelFeedbacks;

    public function __construct(ChannelFeedbacks $channelFeedbacks)
    {
        $this->channelFeedbacks = $channelFeedbacks;
    }


    public function createChannelRatingSummary($channelCriteriaRatings, $average)
    {
        $ratings = array();
        $ratings[] = new RatingSummary('Overall', $average);

        foreach ($channelCriteriaRatings as $singleCriteria) {
            $ratings[] = new RatingSummary(
                $singleCriteria['name'],
                $this->channelFeedbacks->roundHalfUp($singleCriteria['rating'])
            );
        }
        return $ratings;
    }

    public function createFeedbackPage(PaginationInterface $page)
    {
        $lastPageNumber = ceil($page->getTotalItemCount() / $page->getItemNumberPerPage());
        $feedbackSummaryPage = new FeedbackSummaryPage($page->getCurrentPageNumber(), $lastPageNumber);
        foreach ($page as $feedback) {
            $feedbackSummaryPage->addItem($this->createFeedbackSummaryItem($feedback));
        }
        return $feedbackSummaryPage;
    }

    private function createFeedbackSummaryItem($feedback)
    {
        $singleSummary = new FeedbackSummaryItem();
        $singleSummary = $this->addServiceSummary($singleSummary, $feedback->getService());
        $singleSummary->setRatings($this->createFeedbackRatingSummary($feedback));
        $singleSummary->setFeedback($feedback);
        return $singleSummary;
    }

    private function addServiceSummary(FeedbackSummaryItem $singleSummary, ServiceEntity $service)
    {
        $serviceGroup = $service->getServiceGroup();
        $serviceProvider = $service->getServiceProvider();

        $singleSummary->setServiceTypeName($serviceGroup->getName());
        $serviceProviderInfo = $serviceProvider->getFirstname() . ' ' . $serviceProvider->getLastname();
        $singleSummary->setServiceProviderInfo($serviceProviderInfo);
        return $singleSummary;
    }

    private function createFeedbackRatingSummary(FeedbackEntity $feedback)
    {
        $ratings = array();
        $ratings[] = new RatingSummary(
            'Overall',
            $this->channelFeedbacks->getFeedbackRatingAverage($feedback->getId())
        );

        foreach ($feedback->getFeedbackRating() as $criteria) {
            $criteriaName = $criteria->getRatingCriteria()->getRatingCriteria()->getName();
            $ratings[] = new RatingSummary($criteriaName, $criteria->getRating());
        }
        return $ratings;
    }
}