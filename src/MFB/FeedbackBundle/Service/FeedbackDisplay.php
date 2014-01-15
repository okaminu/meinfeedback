<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use MFB\FeedbackBundle\ChannelFeedbacks;
use MFB\RatingBundle\Entity\RatingSummary;
use MFB\FeedbackBundle\Entity\FeedbackSummary;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;

class FeedbackDisplay
{
    private $entityManager;

    private $feedbackOrder;
    
    private $ratingBounds;
    
    private $elementsPerPage;
    
    public function __construct(EntityManager $em, $feedbackOrder, $ratingBoundaries, $feedbacksPerPage)
    {
        $this->entityManager = $em;
        $this->feedbackOrder = $feedbackOrder;
        $this->ratingBounds = $ratingBoundaries;
        $this->elementsPerPage = $feedbacksPerPage;
    }

    public function getChannelFeedbacks($channelId)
    {
        return new ChannelFeedbacks(
            $this->entityManager,
            $this->feedbackOrder,
            $this->ratingBounds,
            $this->elementsPerPage,
            $channelId
        );
    }
}