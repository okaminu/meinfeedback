<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Entity\ChannelServiceType as ChannelServiceEntity;
use MFB\ServiceBundle\Entity\ServiceType;
use MFB\ServiceBundle\ServiceException;

class ChannelServiceType
{
    private $entityManager;

    private $channelService;

    public function __construct(EntityManager $em, Channel $channelService)
    {
        $this->entityManager = $em;
        $this->channelService = $channelService;
    }

    public function createNewServiceType($accountId, ServiceType $serviceType)
    {
        $accountChannel = $this->channelService->findByAccountId($accountId);
        $cse = new ChannelServiceEntity();
        $cse->setChannel($accountChannel);
        $cse->setServiceType($serviceType);
        return $cse;
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

    public function findByAccountId($accountId)
    {
        $accountChannel = $this->channelService->findByAccountId($accountId);
        $serviceProvider = $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceType')->findBy(
            array('channel' => $accountChannel)
        );
        return $serviceProvider;
    }

    public function findVisibleByAccountId($accountId)
    {
        $accountChannel = $this->channelService->findByAccountId($accountId);
        $serviceProvider = $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceType')->findBy(
            array('channel' => $accountChannel, 'visibility' => 1)
        );
        return $serviceProvider;
    }
    public function hasVisible($accountId)
    {
        $service = $this->findVisibleByAccountId($accountId);
        if (count($service) > 0) {
            return true;
        }
        return false;
    }
}