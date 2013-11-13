<?php
namespace MFB\ServiceBundle\Manager;

use MFB\CustomerBundle\Entity\Customer;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;

class Service
{
    private $serviceDescription;

    private $serviceDate;

    private $serviceIdReference;

    private $accountId;

    private $channelId;

    private $customer;

    public function __construct(
        $accountId,
        $channelId,
        Customer $customer,
        $serviceDescription,
        $serviceDate,
        $serviceIdReference
    ) {
        $this->serviceDescription = $serviceDescription;
        $this->serviceDate = $serviceDate;
        $this->serviceIdReference = $serviceIdReference;
        $this->accountId = $accountId;
        $this->channelId = $channelId;
        $this->customer = $customer;
    }

    public function createEntity()
    {
        $service = new ServiceEntity();
        $service->setAccountId($this->accountId);
        $service->setChannelId($this->channelId);
        $service->setCustomer($this->customer);

        if ($this->serviceDescription) {
            $service->setDescription($this->serviceDescription);
        }

        if ($this->serviceIdReference) {
            $service->setServiceIdReference($this->serviceIdReference);
        }

        $serviceDate = $this->serviceDate;
        if ($serviceDate['year'] != "" &&
            $serviceDate['month'] != "" &&
            $serviceDate['day'] != "") {
            $service->setDate(new \DateTime(implode('-', $serviceDate)));
        }
        return $service;
    }
}
