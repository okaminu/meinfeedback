<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\ServiceGroup;
use MFB\ServiceBundle\Entity\ServiceProvider;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\ServiceBundle\ServiceException;
use MFB\CustomerBundle\Service\Customer as CustomerService;

class Service
{
    private $entityManager;

    private $customerService;

    public function __construct(EntityManager $em, CustomerService $customer)
    {
        $this->entityManager = $em;
        $this->customerService = $customer;

    }

    public function createNewService($accountId, $customer = null)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        if (!$customer) {
            $customer = $this->customerService->createNewCustomer($accountId);
        }
        $service = $this->getNewServiceEntity($accountChannelId, $accountId);

        $service->setCustomer($customer);

        return $service;
    }

    public function createNewServiceGroup($accountId)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $serviceGroup = $this->getNewServiceGroupEntity($accountChannelId);
        return $serviceGroup;
    }

    public function createNewServiceProvider($accountId)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $serviceProvider = $this->getNewServiceProviderEntity($accountChannelId);
        return $serviceProvider;
    }

    public function store($serviceGroup)
    {
        try {
            $this->saveEntity($serviceGroup);
        } catch (\Exception $ex) {
                throw new ServiceException('Cannot create service');
        }
    }

    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getAccountChannel($accountId)
    {
        $accountChannel = $this->entityManager->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        return $accountChannel;
    }

    private function getNewServiceEntity($accountChannel, $accountId)
    {
        $serviceGroup = new ServiceEntity();
        $serviceGroup->setChannelId($accountChannel);
        $serviceGroup->setAccountId($accountId);
        return $serviceGroup;
    }

    private function getNewServiceGroupEntity($accountChannel)
    {
        $serviceGroup = new ServiceGroup();
        $serviceGroup->setChannelId($accountChannel);
        return $serviceGroup;
    }

    private function getNewServiceProviderEntity($accountChannel)
    {
        $serviceGroup = new ServiceProvider();
        $serviceGroup->setChannelId($accountChannel);
        return $serviceGroup;
    }

    public function getServiceGroupEntity($accountChannelId)
    {
        $serviceGroup = $this->entityManager->getRepository('MFBServiceBundle:ServiceGroup')->findBy(
            array('channelId' => $accountChannelId)
        );
        return $serviceGroup;
    }

    public function getServiceProviderEntity($accountChannelId)
    {
        $serviceProvider = $this->entityManager->getRepository('MFBServiceBundle:ServiceProvider')->findBy(
            array('channelId' => $accountChannelId)
        );
        return $serviceProvider;
    }

}