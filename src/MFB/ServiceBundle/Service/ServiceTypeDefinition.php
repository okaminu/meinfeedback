<?php
namespace MFB\ServiceBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel;
use MFB\ServiceBundle\Entity\ServiceProvider as ServiceProviderEntity;
use MFB\ServiceBundle\Form\ServiceProviderType;
use MFB\ServiceBundle\ServiceException;

class ServiceTypeDefinition
{
    private $entityManager;
    private $serviceType;
    
    public function __construct($em, $serviceType)
    {
        $this->entityManager = $em;
        $this->serviceType = $serviceType;
    }

    public function findByServiceTypeId($serviceTypeId)
    {
        $serviceTypeEntity = $this->serviceType->findById($serviceTypeId);

        $definition = $this->entityManager->getRepository('MFBServiceBundle:ServiceTypeDefinition')->findBy(
            array('serviceType' => $serviceTypeEntity)
        );
        return $definition;
    }
}