<?php


namespace MFB\FeedbackBundle\Event;

use MFB\AccountBundle\Entity\Account;
use MFB\CustomerBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class CustomerAccountEvent extends Event
{
    private $feedbackId;
    private $account;

    private $customer;

    private $request;

    public function __construct($feedbackId, Account $account, Customer $customer, Request $request)
    {
        $this->feedbackId = $feedbackId;
        $this->account = $account;
        $this->customer = $customer;
        $this->request = $request;
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
    public function getAccount()
    {
        return $this->account;
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
    public function getRequest()
    {
        return $this->request;
    }


}
