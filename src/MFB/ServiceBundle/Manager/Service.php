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

    private $serviceEntity;

    public function __construct(
        $accountId,
        $channelId,
        Customer $customer,
        $serviceDescription,
        $serviceDate,
        $serviceIdReference,
        ServiceEntity $service
    ) {
        $this->serviceDescription = $serviceDescription;
        $this->serviceDate = $serviceDate;
        $this->serviceIdReference = $serviceIdReference;
        $this->accountId = $accountId;
        $this->channelId = $channelId;
        $this->customer = $customer;
        $this->serviceEntity = $service;
    }

    public function createEntity()
    {
        $service = $this->serviceEntity;
        $service->setAccountId($this->accountId);
        $service->setChannelId($this->channelId);
        $service->setCustomer($this->customer);

        if ($this->serviceDescription) {
            $service->setDescription($this->serviceDescription);
        }

        if ($this->serviceIdReference) {
            $service->setServiceIdReference($this->serviceIdReference);
        }

        if ($this->serviceDate) {
            $service->setDate($this->serviceDate);
        }
        $this->serviceEntity = $service;
        return $service;
    }
}
