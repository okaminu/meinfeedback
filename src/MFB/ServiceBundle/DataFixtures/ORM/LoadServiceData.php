<?php
namespace MFB\ServiceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\RatingBundle\Entity\Rating;
use MFB\ServiceBundle\Entity\Business;
use MFB\ServiceBundle\Entity\ServiceType;
use MFB\ServiceBundle\Entity\ServiceTypeCriteria;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

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

    private $serviceTypeCriterias = array(
        array('name' => 'Lawyers', 'criterias' => array('Competence', 'Skill', 'Communication', 'Knowledge')),
        array('name' => 'Medical practitioner', 'criterias' => array('Knowledge', 'Skill', 'Availability')),
        array('name' => 'Car Seller', 'criterias' => array('Qualtiy', 'Support', 'Communication', 'Price')),
        array('name' => 'Grocery', 'criterias' => array('Qualtiy', 'Price', 'Speed')),
        array('name' => 'Hardware', 'criterias' => array('Quality', 'Support', 'Delivery', 'Service')),
        array('name' => 'Bicycles', 'criterias' => array('Speed', 'Price', 'Service', 'Support')),
        array('name' => 'Restaurant', 'criterias' => array('Speed', 'Service', 'Enviroment', 'Price')),
        array('name' => 'Hotel', 'criterias' => array('Speed', 'Service', 'Enviroment', 'Price')),
        array('name' => 'Fashion', 'criterias' => array('Service', 'Price', 'Quality', 'Support')),
        array('name' => 'Clothes', 'criterias' => array('Service', 'Price', 'Quality', 'Support')),
        array('name' => 'Software', 'criterias' => array('Service', 'Price', 'Quality', 'Support', 'Speed'))
    );

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadServiceBusiness($manager);
        $this->loadServiceTypeCriterias($manager);
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

    private function loadServiceTypeCriterias(ObjectManager $manager)
    {
        foreach ($this->serviceTypeCriterias as $type) {
            $serviceType = $manager->getRepository('MFBServiceBundle:ServiceType')->findOneBy(
                array('name' => $type['name'])
            );
            $this->linkCriteriasToServiceType($manager, $type, $serviceType);
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
                $rating = $this->createNewRatingEntity($manager, $criteria);
                $manager->persist($rating);
                $this->addReference("rating-{$criteria}", $rating);
            }
            $serviceCriteria = $this->createServiceCriteriaEntity($serviceType, $rating);
            $manager->persist($serviceCriteria);
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

    private function createServiceCriteriaEntity($serviceType, $rating)
    {
        $serviceCriteria = new ServiceTypeCriteria();
        $serviceCriteria->setServiceType($serviceType);
        $serviceCriteria->setRating($rating);
        return $serviceCriteria;
    }


}