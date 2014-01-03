<?php
namespace MFB\FeedbackBundle\Entity;

class FeedbackSummary
{
    private $feedback;

    private $feedbackRating;

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