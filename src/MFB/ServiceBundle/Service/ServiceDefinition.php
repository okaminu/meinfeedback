<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\ServiceDefinition as DefinitionEntity;
use MFB\ServiceBundle\ServiceException;

class ServiceDefinition
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNew()
    {
        return new DefinitionEntity();
    }

    public function createCustom($name)
    {
        $entity = $this->createNew();
        $entity->setIsCustom(true);
        $entity->setName($name);
        return $entity;
    }


    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Cannot save service definition');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
