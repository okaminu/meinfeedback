<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Service\ServiceType as ServiceTypeService;
use MFB\ServiceBundle\Service\ServiceProvider as ServiceProviderService;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\ServiceBundle\Form\ServiceType;
use MFB\ServiceBundle\ServiceException;
use MFB\CustomerBundle\Service\Customer as CustomerService;

class Service
{
    private $entityManager;

    private $customerService;

    private $serviceProvider;

    private $serviceType;

    public function __construct(
        EntityManager $em,
        CustomerService $customer,
        ServiceProviderService $serviceProvider,
        ServiceTypeService $serviceType
    ) {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->serviceProvider = $serviceProvider;
        $this->serviceType = $serviceType;
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


    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Email already exists');
        }
    }

    /**
     * @param $accountId
     * @return ServiceType
     */
    public function getServiceType($accountId)
    {
        $serviceType = $this->serviceType->findVisibleByAccountId($accountId);
        $serviceProvider = $this->serviceProvider->findVisibleByAccountId($accountId);
        $serviceType = new ServiceType($serviceProvider, $serviceType);
        return $serviceType;
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
        $serviceType = new ServiceEntity();
        $serviceType->setChannelId($accountChannel);
        $serviceType->setAccountId($accountId);
        return $serviceType;
    }

}
