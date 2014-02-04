<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Entity\ChannelServiceType as ChannelServiceEntity;
use MFB\ServiceBundle\Service\ServiceType;
use MFB\ServiceBundle\ServiceException;

class ChannelServiceType
{
    private $entityManager;

    private $channelService;

    private $serviceType;

    public function __construct(EntityManager $em, Channel $channelService, ServiceType $serviceType)
    {
        $this->entityManager = $em;
        $this->channelService = $channelService;
        $this->serviceType = $serviceType;
    }

    public function createNew($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $cse = new ChannelServiceEntity();
        $cse->setChannel($accountChannel);

        return $cse;
    }

    public function createStoreNew($accountId, $serviceTypeId)
    {
        $st = $this->createNew($accountId, $serviceTypeId);
        $this->store($st);
    }


    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Service is already selected');
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

    public function findByChannelId($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $serviceProvider = $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceType')->findBy(
            array('channel' => $accountChannel)
        );
        return $serviceProvider;
    }


    public function findVisibleByChannelId($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);
        $serviceProvider = $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceType')->findBy(
            array('channel' => $accountChannel, 'visibility' => 1)
        );
        return $serviceProvider;
    }
}