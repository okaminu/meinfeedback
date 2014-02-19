<?php
namespace MFB\SetupWizardBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel;
use MFB\SetupWizardBundle\SetupWizardException;
use MFB\SetupWizardBundle\Entity\WizardStep as WizardStepEntity;

class WizardStep
{
    private $entityManager;
    private $stepStatuses;
    private $channelService;

    public function __construct(EntityManager $em, $stepStatuses, Channel $channelService)
    {
        $this->entityManager = $em;
        $this->stepStatuses = $stepStatuses;
        $this->channelService = $channelService;
    }

    public function createNew($channelId)
    {
        $entity = new WizardStepEntity();
        $entity->setChannel($this->channelService->findById($channelId));
        return $entity;
    }

    public function createNewPending($channelId)
    {
        $entity = $this->createNew($channelId);
        $entity->setStatus($this->stepStatuses['pending']);
        return $entity;
    }

    public function store($entity)
    {
        try {
            $this->saveEntity($entity);
        } catch (DBALException $ex) {
            throw new SetupWizardException('Cannot save wizard step');
        }
    }

    public function findPendingByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBSetupWizardBundle:WizardStep')->findBy(
            array('channel' => $channelId, 'status' => $this->stepStatuses['pending'])
        );
    }

    public function findByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBSetupWizardBundle:WizardStep')->findBy(
            array('channel' => $channelId)
        );
    }

    public function hasSteps($channelId)
    {
        $steps = $this->findByChannelId($channelId);
        if (count($steps) > 0) {
            return true;
        }
        return false;
    }

    public function findByChannelIdAndName($channelId, $name)
    {
        return $this->entityManager->getRepository('MFBSetupWizardBundle:WizardStep')->findOneBy(
            array('channel' => $channelId, 'name' => $name)
        );
    }

    public function setStepStatus($channelId, $name, $status)
    {
        $step = $this->findByChannelIdAndName($channelId, $name);
        $step->setStatus($this->stepStatuses[$status]);
        $this->store($step);
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove($entity)
    {
        $this->removeEntity($entity);
    }

    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}