<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\ServiceGroup as ServiceGroupEntity;
use MFB\ServiceBundle\ServiceException;

class ServiceGroup
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewServiceGroup($accountId)
    {
        $accountChannelId = $this->getAccountChannelId($accountId);
        $service = $this->getNewServiceGroupEntity($accountChannelId);
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


    private function getNewServiceGroupEntity($accountChannel)
    {
        $serviceGroup = new ServiceGroupEntity();
        $serviceGroup->setChannelId($accountChannel);
        return $serviceGroup;
    }

    public function findServiceGroupEntity($accountChannelId)
    {
        $serviceProvider = $this->entityManager->getRepository('MFBServiceBundle:ServiceGroup')->findBy(
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