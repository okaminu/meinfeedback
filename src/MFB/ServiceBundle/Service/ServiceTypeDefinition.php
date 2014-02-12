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
    
    public function __construct($em)
    {
        $this->entityManager = $em;
    }

    public function findByServiceTypeId($serviceTypeId)
    {
        $definition = $this->entityManager->getRepository('MFBServiceBundle:ServiceTypeDefinition')->findBy(
            array('serviceType' => $serviceTypeId)
        );
        return $definition;
    }
}