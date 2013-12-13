<?php


namespace MFB\FeedbackBundle\Event;

use MFB\CustomerBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

class CustomerAccountEvent extends Event
{
    private $feedbackId;
    private $email;
    private $customer;
    private $feedbackText;
    private $feedbackRating;
    private $invite;

    public function __construct($feedbackId, $email, Customer $customer, $feedbackText, $feedbackRating, $invite = null)
    {
        $this->feedbackId = $feedbackId;
        $this->email = $email;
        $this->customer = $customer;
        $this->feedbackText = $feedbackText;
        $this->feedbackRating = $feedbackRating;
        $this->invite = $invite;
    }

    /**
     * @return mixed
     */
    public function getFeedbackId()
    {
        return $this->feedbackId;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return mixed
     */
    public function getFeedbackText()
    {
        return $this->feedbackText;
    }

    /**
     * @return mixed
     */
    public function getFeedbackRating()
    {
        return $this->feedbackRating;
    }

    /**
     * @return null
     */
    public function getInvite()
    {
        return $this->invite;
    }

    public function hasInvite()
    {
        return null === $this->invite;
    }

}
