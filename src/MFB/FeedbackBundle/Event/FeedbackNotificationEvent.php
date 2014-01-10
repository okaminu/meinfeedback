<?php


namespace MFB\FeedbackBundle\Event;

use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback;
use Symfony\Component\EventDispatcher\Event;

class FeedbackNotificationEvent extends Event
{
    private $feedback;
    private $email;
    private $customer;
    private $invite;

    public function __construct(Feedback $feedback, $email, Customer $customer, $invite = null)
    {
        $this->feedback = $feedback;
        $this->email = $email;
        $this->customer = $customer;
        $this->invite = $invite;
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
    public function getFeedback()
    {
        return $this->feedback;
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
