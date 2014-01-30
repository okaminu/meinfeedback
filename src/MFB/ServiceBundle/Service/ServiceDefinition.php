<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel;
use MFB\ServiceBundle\ServiceException;
use MFB\ServiceBundle\Entity\ServiceDefinition as DefinitionEntity;

class ServiceDefinition
{
    private $entityManager;

    private $channelService;
    
    public function __construct(EntityManager $em, Channel $channel)
    {
        $this->entityManager = $em;
        $this->channelService = $channel;
    }

    public function createNew($channelId)
    {
        $channel = $this->channelService->findById($channelId);

        $definition = new DefinitionEntity();
        $definition->setChannel($channel);

        return $definition;
    }


    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Definition already exists');
        }
    }

    public function remove($definition)
    {
        try {
            $this->removeEntity($definition);
        } catch (DBALException $ex) {
            throw new ServiceException('Cannot remove definition');
        }
    }

    public function findByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceDefinition')->findBy(
            array('channel' => $channelId)
        );
    }

    public function findByChannelIdAndId($channelId, $id)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceDefinition')->findOneBy(
            array('channel' => $channelId, 'id' => $id)
        );
    }


    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

}
