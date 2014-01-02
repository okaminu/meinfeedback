<?php

namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChannelRatingCriteria
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ChannelRatingCriteria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(
     * targetEntity="MFB\ChannelBundle\Entity\AccountChannel",
     * inversedBy="ratingCriteria",
     * cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\RatingBundle\Entity\Rating")
     * @ORM\JoinColumn(name="rating_criteria_id", referencedColumnName="id")
     */
    private $ratingCriteria;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\FeedbackBundle\Entity\FeedbackRating", mappedBy="ratingCriteria")
     */
    private $feedbackRating;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isEnabled", type="boolean", options={"default" : 1})
     */
    private $isEnabled = 1;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return ChannelRatingCriteria
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    
        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return ChannelRatingCriteria
     */
    public function setChannel(\MFB\ChannelBundle\Entity\AccountChannel $channel = null)
    {
        $this->channel = $channel;
    
        return $this;
    }

    /**
     * Get channel
     *
     * @return \MFB\ChannelBundle\Entity\AccountChannel 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set ratingCriteria
     *
     * @param \MFB\RatingBundle\Entity\Rating $ratingCriteria
     * @return ChannelRatingCriteria
     */
    public function setRatingCriteria(\MFB\RatingBundle\Entity\Rating $ratingCriteria = null)
    {
        $this->ratingCriteria = $ratingCriteria;
    
        return $this;
    }

    /**
     * Get ratingCriteria
     *
     * @return \MFB\RatingBundle\Entity\Rating 
     */
    public function getRatingCriteria()
    {
        return $this->ratingCriteria;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedbackRating = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add feedbackRating
     *
     * @param \MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating
     * @return ChannelRatingCriteria
     */
    public function addFeedbackRating(\MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating)
    {
        $this->feedbackRating[] = $feedbackRating;
    
        return $this;
    }

    /**
     * Remove feedbackRating
     *
     * @param \MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating
     */
    public function removeFeedbackRating(\MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating)
    {
        $this->feedbackRating->removeElement($feedbackRating);
    }

    /**
     * Get feedbackRating
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedbackRating()
    {
        return $this->feedbackRating;
    }
}