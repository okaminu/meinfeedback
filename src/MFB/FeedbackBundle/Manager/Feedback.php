<?php
namespace MFB\FeedbackBundle\Manager;

use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;

class Feedback
{
    private $feedbackContent;

    private $feedbackRating;

    private $accountId;

    private $channelId;

    private $customer;

    public function __construct(
        $accountId,
        $channelId,
        Customer $customer,
        $feedbackContent,
        $feedbackRating
    ) {
        $this->feedbackContent = $feedbackContent;
        $this->feedbackRating = $feedbackRating;
        $this->accountId = $accountId;
        $this->channelId = $channelId;
        $this->customer = $customer;
    }

    public function createEntity()
    {
        $rating = null;
        $feedback = new FeedbackEntity();
        $feedback->setAccountId($this->accountId);
        $feedback->setChannelId($this->channelId);
        $feedback->setCustomer($this->customer);
        $feedback->setContent($this->feedbackContent);

        $requestRating = (int)$this->feedbackRating;

        if (($requestRating > 0) && ($requestRating <= 5)) {
            $rating = $requestRating;
        }

        $feedback->setRating($rating);
        return $feedback;
    }
}
