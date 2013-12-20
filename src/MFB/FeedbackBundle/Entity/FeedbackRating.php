<?php

namespace MFB\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeedbackRating
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FeedbackRating
{

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="smallint")
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="MFB\FeedbackBundle\Entity\Feedback", inversedBy="feedbackRating")
     * @ORM\JoinColumn(name="feedback_id", referencedColumnName="id")
     */
    private $feedback;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="MFB\ChannelBundle\Entity\ChannelRatingCriteria", inversedBy="feedbackRating")
     * @ORM\JoinColumn(name="channel_rating_criteria_id", referencedColumnName="id")
     */
    private $ratingCriteria;

    /**
     * Set rating
     *
     * @param integer $rating
     * @return FeedbackRating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set feedback
     *
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return FeedbackRating
     */
    public function setFeedback(\MFB\FeedbackBundle\Entity\Feedback $feedback = null)
    {
        $this->feedback = $feedback;
    
        return $this;
    }

    /**
     * Get feedback
     *
     * @return \MFB\FeedbackBundle\Entity\Feedback 
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set ratingCriteria
     *
     * @param \MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria
     * @return FeedbackRating
     */
    public function setRatingCriteria(\MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria = null)
    {
        $this->ratingCriteria = $ratingCriteria;
    
        return $this;
    }

    /**
     * Get ratingCriteria
     *
     * @return \MFB\ChannelBundle\Entity\ChannelRatingCriteria 
     */
    public function getRatingCriteria()
    {
        return $this->ratingCriteria;
    }
}