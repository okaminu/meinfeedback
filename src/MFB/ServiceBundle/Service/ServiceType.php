<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\ORM\EntityManager;

class ServiceType
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function findByBusinessId($businessId)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceType')->findBy(
            array('business' => $businessId)
        );
    }

    public function findById($id)
    {
        return $this->entityManager->getRepository('MFBServiceBundle:ServiceType')->find($id);
    }

}
