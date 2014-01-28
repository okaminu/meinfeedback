<?php
namespace MFB\ServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\ServiceBundle\Entity\Business;
use MFB\ServiceBundle\Entity\ServiceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServiceData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var $container \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $businessList = $this->container->getParameter('mfb_service_business.defaults');
        $serviceTypesList = $this->container->getParameter('mfb_service_types.defaults');

        foreach ($businessList as $key => $businessName) {
            $businessEntity = $this->createNewBusinessEntity($businessName);
            $manager->persist($businessEntity);

            $this->loadServiceTypesForBusiness($manager, $serviceTypesList[$key], $businessEntity);
        }
        $manager->flush();
    }

    private function createNewBusinessEntity($name)
    {
        $entity = new Business();
        $entity->setName($name);
        return $entity;
    }

    private function createNewServiceTypeEntity($name, $business)
    {
        $entity = new ServiceType();
        $entity->setBusiness($business);
        $entity->setName($name);
        return $entity;
    }

    private function loadServiceTypesForBusiness(ObjectManager $manager, $serviceTypes, $businessEntity)
    {
        foreach ($serviceTypes as $type) {
            $typeEntity = $this->createNewServiceTypeEntity($type, $businessEntity);
            $manager->persist($typeEntity);
        }
    }

}