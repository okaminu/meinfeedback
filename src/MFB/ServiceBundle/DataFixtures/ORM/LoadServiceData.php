<?php
namespace MFB\ServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\RatingBundle\Entity\Rating;
use MFB\ServiceBundle\Entity\Business;
use MFB\ServiceBundle\Entity\ServiceDefinition;
use MFB\ServiceBundle\Entity\ServiceType;
use MFB\ServiceBundle\Entity\ServiceTypeCriteria;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MFB\ServiceBundle\Entity\ServiceTypeDefinition;

class LoadServiceData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $businessList = array(
        array(
            'name' => 'Service Providers',
            'multiple' => 0,
            'service_types' =>
                array('Lawyers', 'Medical practitioner')
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

    private $serviceTypeCriteriasDefinitions = array(
        array('name' => 'Lawyers', 'criterias' => array('Competence', 'Skill', 'Communication', 'Knowledge'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Medical practitioner', 'criterias' => array('Knowledge', 'Skill', 'Availability'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Car Seller', 'criterias' => array('Qualtiy', 'Support', 'Communication', 'Price'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Grocery', 'criterias' => array('Qualtiy', 'Price', 'Speed'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Hardware', 'criterias' => array('Quality', 'Support', 'Delivery', 'Service'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Bicycles', 'criterias' => array('Speed', 'Price', 'Service', 'Support'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Restaurant', 'criterias' => array('Speed', 'Service', 'Enviroment', 'Price'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Hotel', 'criterias' => array('Speed', 'Service', 'Enviroment', 'Price'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Fashion', 'criterias' => array('Service', 'Price', 'Quality', 'Support'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Clothes', 'criterias' => array('Service', 'Price', 'Quality', 'Support'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3')),
        array('name' => 'Software', 'criterias' => array('Service', 'Price', 'Quality', 'Support', 'Speed'),
            'definition' => array('Test definition 1', 'Test definition 2', 'Test definition 3'))
    );

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadServiceBusiness($manager);
        $this->loadServiceTypeCriteriasDefinitions($manager);
    }

    private function loadServiceBusiness(ObjectManager $manager)
    {
        foreach ($this->businessList as $business) {
            $businessEntity = $this->createNewBusinessEntity($business['name'], $business['multiple']);
            $manager->persist($businessEntity);
            $this->loadServiceTypesForBusiness($manager, $business['service_types'], $businessEntity);
        }
        $manager->flush();
    }

    private function loadServiceTypeCriteriasDefinitions(ObjectManager $manager)
    {
        foreach ($this->serviceTypeCriteriasDefinitions as $type) {
            $serviceType = $manager->getRepository('MFBServiceBundle:ServiceType')->findOneBy(
                array('name' => $type['name'])
            );
            $this->linkCriteriasToServiceType($manager, $type, $serviceType);
            $this->linkDefinitionsToServiceType($manager, $type, $serviceType);
        }
        $manager->flush();
    }

    private function loadServiceTypesForBusiness(ObjectManager $manager, $serviceTypes, $businessEntity)
    {
        foreach ($serviceTypes as $type) {
            $typeEntity = $this->createNewServiceTypeEntity($type, $businessEntity);
            $manager->persist($typeEntity);
        }
        $manager->flush();
    }

    private function linkCriteriasToServiceType(ObjectManager $manager, $type, $serviceType)
    {
        foreach ($type['criterias'] as $criteria) {
            try {
                $rating = $this->getReference("rating-{$criteria}");
            } catch (\Exception $ex) {
                $rating = $this->createNewRatingEntity($criteria);
                $manager->persist($rating);
                $this->addReference("rating-{$criteria}", $rating);
            }
            $serviceCriteria = $this->createServiceCriteriaEntity($serviceType, $rating);
            $manager->persist($serviceCriteria);
        }
        $manager->flush();
    }

    private function linkDefinitionsToServiceType(ObjectManager $manager, $type, $serviceType)
    {
        foreach ($type['definition'] as $definitionName) {

            $definition = $this->getDefinition($manager, $definitionName);
            if ($definition == null) {
                $definition = $this->createNewDefinitionEntity($definitionName);
                $manager->persist($definition);
            }

            $service = $this->createServiceTypeDefinitionEntity($serviceType, $definition);
            $manager->persist($service);
        }
        $manager->flush();
    }

    private function createNewBusinessEntity($name, $multiple)
    {
        $entity = new Business();
        $entity->setName($name);
        $entity->setIsMultipleServices($multiple);
        $entity->setIsCustom(false);
        return $entity;
    }

    private function createNewServiceTypeEntity($name, $business)
    {
        $entity = new ServiceType();
        $entity->setBusiness($business);
        $entity->setName($name);
        $entity->setIsCustom(false);
        return $entity;
    }

    private function createNewRatingEntity($criteria)
    {
        $rating = new Rating();
        $rating->setName($criteria);
        return $rating;
    }

    private function createNewDefinitionEntity($definition)
    {
        $definitionEntity = new ServiceDefinition();
        $definitionEntity->setName($definition);
        $definitionEntity->setIsCustom(false);
        return $definitionEntity;
    }

    private function createServiceCriteriaEntity($serviceType, $rating)
    {
        $serviceCriteria = new ServiceTypeCriteria();
        $serviceCriteria->setServiceType($serviceType);
        $serviceCriteria->setRating($rating);
        return $serviceCriteria;
    }

    private function createServiceTypeDefinitionEntity($serviceType, $defitinion)
    {
        $service = new ServiceTypeDefinition();
        $service->setServiceType($serviceType);
        $service->setServiceDefinition($defitinion);
        return $service;
    }

    private function getDefinition($manager, $defName)
    {
         return $manager->getRepository("MFBServiceBundle:ServiceDefinition")->findOneBy(
             array('name' => $defName)
         );
    }


}