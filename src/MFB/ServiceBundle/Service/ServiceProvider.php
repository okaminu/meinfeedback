<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\ServiceProvider as ServiceProviderEntity;
use MFB\ServiceBundle\ServiceException;

class ServiceProvider
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewServiceProvider($accountId)
    {
        $accountChannelId = $this->getAccountChannelId($accountId);
        $service = $this->getNewServiceProviderEntity($accountChannelId);
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

    private function getNewServiceProviderEntity($accountChannel)
    {
        $serviceGroup = new ServiceProviderEntity();
        $serviceGroup->setChannelId($accountChannel);
        return $serviceGroup;
    }

    public function findByChannelId($accountChannelId)
    {
        $serviceProvider = $this->entityManager->getRepository('MFBServiceBundle:ServiceProvider')->findAll(
            array('channelId' => $accountChannelId)
        );
        return $serviceProvider;
    }


    /**
     * @param $accountId
     * @return int
     */
    private function getAccountChannelId($accountId)
    {
        return $this->getAccountChannel($accountId)->getId();
    }
}