<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Entity\ChannelServiceDefinition as ChannelDefinitionEntity;
use MFB\ServiceBundle\Entity\ServiceDefinition;

class ChannelServiceDefinition
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
        $definition = new ChannelDefinitionEntity();
        $channel = $this->channelService->findById($channelId);
        $definition->setChannel($channel);
        $definition->setServiceDefinition(new ServiceDefinition());
        return $definition;
    }

    public function createNewCustom($channelId)
    {
        $channelDefinition = $this->createNew($channelId);
        $channelDefinition->getServiceDefinition()->setIsCustom(true);
        return $channelDefinition;
    }

    public function store($channel)
    {
        try {
            $this->saveEntity($channel);
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot store channel data');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove($definition)
    {
        try {
            $this->removeEntity($definition);
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot remove definition');
        }
    }

    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush($entity);
    }

    public function removeList($definitionList)
    {
        try {
            foreach ($definitionList as $definition) {
                $this->entityManager->remove($definition);
            }
            $this->entityManager->flush();
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot remove definition');
        }
    }

    public function findByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceDefinition')->findBy(
            array('channel' => $channelId)
        );
    }

    public function findDefinitionIdsByChannelId($channelId)
    {
        $channelDefs = $this->findByChannelId($channelId);

        $definitionsIds = array();
        foreach ($channelDefs as $definition) {
            $definitionsIds[] = $definition->getServiceDefinition()->getId();
        }
        return $definitionsIds;

    }

    public function findByChannelAndDefinition($channelId, $definitionId)
    {
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelServiceDefinition')->findOneBy(
            array('serviceDefinition' => $definitionId, 'channel' => $channelId)
        );
    }

    public function hasDefinitions($channelId)
    {
        if (count($this->findByChannelId($channelId)) > 0) {
            return true;
        }
        return false;
    }

}
