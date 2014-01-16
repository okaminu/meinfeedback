<?php
namespace MFB\FeedbackBundle\Entity;

class FeedbackSummaryItem
{
    private $feedback;

    private $feedbackRatings;

    private $serviceProviderInfo;

    private $serviceTypeName;

    /**
     * @param mixed $feedbackRatings
     */
    public function setRatings($feedbackRatings)
    {
        $this->feedbackRatings = $feedbackRatings;
    }

    /**
     * @return mixed
     */
    public function getRatings()
    {
        return $this->feedbackRatings;
    }

    /**
     * @param mixed $serviceProviderInfo
     */
    public function setServiceProviderInfo($serviceProviderInfo)
    {
        $this->serviceProviderInfo = $serviceProviderInfo;
    }

    /**
     * @return mixed
     */
    public function getServiceProviderInfo()
    {
        return $this->serviceProviderInfo;
    }

    /**
     * @param mixed $serviceTypeName
     */
    public function setServiceTypeName($serviceTypeName)
    {
        $this->serviceTypeName = $serviceTypeName;
    }

    /**
     * @return mixed
     */
    public function getServiceTypeName()
    {
        return $this->serviceTypeName;
    }

    /**
     * @param mixed $feedback
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * @return mixed
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    public function getRatingByName($name)
    {
        foreach ($this->feedbackRatings as $rating) {
            if ($rating->getName() == $name) {
                return $rating;
            }
        }
    }
}