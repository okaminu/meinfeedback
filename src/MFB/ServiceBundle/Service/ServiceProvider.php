<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel;
use MFB\ServiceBundle\Entity\ServiceProvider as ServiceProviderEntity;
use MFB\ServiceBundle\Form\ServiceProviderType;
use MFB\ServiceBundle\ServiceException;

class ServiceProvider
{
    private $entityManager;
    
    private $honorific;

    private $channelService;

    public function __construct(EntityManager $em, $honorific, Channel $cs)
    {
        $this->entityManager = $em;
        $this->honorific = $honorific;
        $this->channelService = $cs;
    }

    public function createNewServiceProvider($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $service = $this->getNewServiceProviderEntity($accountChannel);
        return $service;
    }

    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Team member already exists');
        }
    }

    public function getType()
    {
        return new ServiceProviderType($this->honorific);
    }


    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }


    private function getNewServiceProviderEntity($accountChannel)
    {
        $serviceGroup = new ServiceProviderEntity();
        $serviceGroup->setChannel($accountChannel);
        return $serviceGroup;
    }

    public function findByChannelId($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $serviceProvider = $this->entityManager->getRepository('MFBServiceBundle:ServiceProvider')->findBy(
            array('channel' => $accountChannel)
        );
        return $serviceProvider;
    }

    public function findVisibleByChannelId($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $serviceProvider = $this->entityManager->getRepository('MFBServiceBundle:ServiceProvider')->findBy(
            array('channel' => $accountChannel, 'visibility' => 1)
        );
        return $serviceProvider;
    }
    
    public function hasVisibleServiceProviders($channelId)
    {
        $service = $this->findVisibleByChannelId($channelId);
        if (count($service) > 0) {
            return true;
        }
        return false;
    }
    
    public function getHonorifics()
    {
        return $this->honorific;
    }
}