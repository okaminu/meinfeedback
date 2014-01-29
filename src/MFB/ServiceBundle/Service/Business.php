<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ServiceBundle\Entity\Business as BusinessEntity;
use MFB\ServiceBundle\ServiceException;

class Business
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewBusiness()
    {
        return new BusinessEntity();
    }

    public function findById($id)
    {
        return $this->entityManager->getRepository("MFBServiceBundle:Business")->find($id);
    }

    public function findAll()
    {
        return $this->entityManager->getRepository("MFBServiceBundle:Business")->findAll();
    }

    public function store($service)
    {
        try {
            $this->saveEntity($service);
        } catch (DBALException $ex) {
            throw new ServiceException('Business already exists');
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
}