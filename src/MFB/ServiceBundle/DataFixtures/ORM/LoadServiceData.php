<?php
namespace MFB\ServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\ServiceBundle\Entity\Business;
use MFB\ServiceBundle\Entity\ServiceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServiceData implements FixtureInterface
{
    private $businessList;

    public function __construct()
    {
        $this->businessList = array(
            array(
                'name' => 'Service Providers',
                'multiple' => 0,
                'service_types' =>
                    array('Lawyers', 'Medical practicioner')
            ),
            array(
                'name' => 'Sellers and Stores',
                'multiple' => 1,
                'service_types' =>
                    array('Car Seller', 'Grocery', 'Hardware', 'Bicycles')
            ),
            array(
                'name' => 'Restaurants and Hotels',
                'multiple' => 0,
                'service_types' =>
                    array('Restaurant', 'Hotel')
            ),
            array(
                'name' => 'Producers',
                'multiple' => 1,
                'service_types' =>
                    array('Fashion', 'Clothes', 'Software')
            )
        );
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->businessList as $business) {
            $businessEntity = $this->createNewBusinessEntity($business['name'], $business['multiple']);
            $manager->persist($businessEntity);

            $this->loadServiceTypesForBusiness($manager, $business['service_types'], $businessEntity);
        }
        $manager->flush();
    }

    private function createNewBusinessEntity($name, $multiple)
    {
        $entity = new Business();
        $entity->setName($name);
        $entity->setIsMultipleServices($multiple);
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