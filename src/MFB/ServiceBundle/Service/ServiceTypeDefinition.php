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
    private $channelService;
    
    public function __construct($em, $serviceType, $channelService)
    {
        $this->entityManager = $em;
        $this->serviceType = $serviceType;
        $this->channelService = $channelService;
    }

    public function findByServiceTypeId($serviceTypeId)
    {
        $serviceTypeEntity = $this->serviceType->findById($serviceTypeId);

        $definition = $this->entityManager->getRepository('MFBServiceBundle:ServiceTypeDefinition')->findBy(
            array('serviceType' => $serviceTypeEntity)
        );
        return $definition;
    }

    public function getDefinitionsByChannelServiceTypes($channelId)
    {
        $serviceTypes = $this->channelService->findByChannelId($channelId);

        $definitions = array();
        foreach ($serviceTypes as $type) {
            $definitions = array_merge($definitions, $type->getServiceType()->getDefinitions());
        }
        return $definitions;
    }
}