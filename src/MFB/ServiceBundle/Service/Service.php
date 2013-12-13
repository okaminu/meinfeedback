<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\ServiceGroup;
use MFB\ServiceBundle\Entity\ServiceProvider;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\ServiceBundle\ServiceException;

class Service
{
    private $entityManager;


    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewService($accountId)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $service = $this->getNewServiceEntity($accountChannelId, $accountId);
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

}