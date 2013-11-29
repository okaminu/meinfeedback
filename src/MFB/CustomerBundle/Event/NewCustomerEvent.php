<?php


namespace MFB\CustomerBundle\Event;

use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\ServiceBundle\Entity\Service;
use Symfony\Component\EventDispatcher\Event;

class NewCustomerEvent extends Event
{
    private $service;
    private $customer;
    private $channel;
    private $inviteUrl;

    public function __construct(Customer $customer, AccountChannel $channel, Service $service, $inviteUrl)
    {
        $this->customer = $customer;
        $this->service = $service;
        $this->channel = $channel;
        $this->inviteUrl = $inviteUrl;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return AccountChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return mixed
     */
    public function getInviteUrl()
    {
        return $this->inviteUrl;
    }


}
