<?php
namespace MFB\FeedbackBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\FeedbackException;

/**
 * Class Feedback
 * @package MFB\FeedbackBundle\Manager
 * @deprecated We should leave symfony handle joins
 */
class Feedback
{
    private $feedbackContent;

    private $feedbackRating;

    private $accountId;

    private $channelId;

    private $customer;

    private $feedbackEntity;

    public function __construct(
        $accountId,
        $channelId,
        Customer $customer,
        $feedbackContent,
        $feedbackRating,
        FeedbackEntity $feedbackEntity
    ) {
        $this->feedbackContent = $feedbackContent;
        $this->feedbackRating = $feedbackRating;
        $this->accountId = $accountId;
        $this->channelId = $channelId;
        $this->customer = $customer;
        $this->feedbackEntity = $feedbackEntity;
    }

    public function createEntity()
    {
        $rating = null;
        $feedback = $this->feedbackEntity;
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

    public function saveFeedback(ObjectManager $em, $ratingsEnabled)
    {
        if ($this->feedbackContent == '') {
            throw new FeedbackException('Please leave a feedback');
        }
        $feedbackEntity = $this->createEntity();

        if (($ratingsEnabled == '1') && (is_null($feedbackEntity->getRating()))) {
            throw new FeedbackException('Please select star rating');
        }

        $em->persist($feedbackEntity);
        $em->flush();

        return $feedbackEntity->getId();
    }
}
