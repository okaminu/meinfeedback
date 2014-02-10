<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use \MFB\ServiceBundle\Entity\ServiceType as ServiceTypeEntity;
use MFB\ServiceBundle\ServiceException;

class ServiceType
{
    private $entityManager;

    private $businessService;

    public function __construct(EntityManager $em, Business $businessService)
    {
        $this->entityManager = $em;
        $this->businessService = $businessService;
    }

    public function createNew($businessId)
    {
        $serviceType = new ServiceTypeEntity();
        $serviceType->setBusiness($this->businessService->findById($businessId));

        return $serviceType;
    }

    public function createCustom($businessId, $name)
    {
        $serviceType = $this->createNew($businessId);
        $serviceType->setName($name);
        $serviceType->setIsCustom(true);

        return $serviceType;
    }


    public function findByBusinessId($businessId)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceType')->findBy(
            array('business' => $businessId)
        );
    }

    public function getDefaultByBusinessId($businessId)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceType')->findBy(
            array('business' => $businessId, 'isCustom' => false)
        );
    }

    public function findById($id)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceType')->find($id);
    }

    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Service already exists');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}
