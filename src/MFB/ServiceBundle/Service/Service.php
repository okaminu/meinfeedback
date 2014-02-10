<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel;
use MFB\ChannelBundle\Service\ChannelServiceType;
use MFB\ServiceBundle\Service\ServiceProvider as ServiceProviderService;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\ServiceBundle\Form\ServiceType as ServiceTypeForm;
use MFB\ServiceBundle\ServiceException;
use MFB\CustomerBundle\Service\Customer as CustomerService;

class Service
{
    private $entityManager;

    private $customerService;

    private $serviceProvider;

    private $channelServiceType;

    private $channelService;

    public function __construct(
        EntityManager $em,
        CustomerService $customer,
        ServiceProviderService $serviceProvider,
        ChannelServiceType $channelServiceType,
        Channel $channelService
    ) {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->serviceProvider = $serviceProvider;
        $this->channelServiceType = $channelServiceType;
        $this->channelService = $channelService;
    }

    public function createNewService($channelId, $customer = null)
    {
        $channel = $this->channelService->findById($channelId);
        if (!$customer) {
            $customer = $this->customerService->createNewCustomer($channelId);
        }
        $service = $this->getNewServiceEntity($channel->getId(), $channel->getAccountId());
        $service->setChannelServiceType($this->channelServiceType->createNew($channelId));
        $service->setCustomer($customer);
        return $service;
    }

    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Email already exists');
        }
    }

    public function getServiceFormType($channelId)
    {
        $serviceType = $this->channelServiceType->findVisibleByChannelId($channelId);
        $serviceProvider = $this->serviceProvider->findVisibleByChannelId($channelId);
        $serviceType = new ServiceTypeForm($serviceProvider, $serviceType);
        return $serviceType;
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getNewServiceEntity($channelId, $accountId)
    {
        $serviceType = new ServiceEntity();
        $serviceType->setChannelId($channelId);
        $serviceType->setAccountId($accountId);
        return $serviceType;
    }

}
