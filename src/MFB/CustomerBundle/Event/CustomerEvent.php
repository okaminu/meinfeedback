<?php


namespace MFB\CustomerBundle\Event;

use MFB\CustomerBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class CustomerEvent extends Event
{
    private $request;
    private $customer;

    public function __construct(Customer $customer, Request $request)
    {
        $this->customer = $customer;
        $this->request = $request;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
