<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\ChannelFeedbacks;

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