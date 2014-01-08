<?php
namespace MFB\FeedbackBundle\Entity;

class FeedbackSummary
{
    private $feedback;

    private $feedbackRating;

    private $serviceProviderInfo;

    private $serviceTypeName;

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

    /**
     * @param mixed $feedbackRating
     */
    public function setRating($feedbackRating)
    {
        $this->feedbackRating = $feedbackRating;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->feedbackRating;
    }


}