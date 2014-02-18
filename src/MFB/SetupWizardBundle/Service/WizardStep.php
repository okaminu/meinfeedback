<?php
namespace MFB\SetupWizardBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\SetupWizardBundle\SetupWizardException;
use MFB\SetupWizardBundle\Entity\WizardStep as WizardStepEntity;

class WizardStep
{

    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNew($channelId)
    {
        $entity = new WizardStepEntity();
        $entity->setChannel($channelId);

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