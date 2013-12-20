<?php
namespace MFB\CountryBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\CountryBundle\Entity\Country;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCountryData implements FixtureInterface, ContainerAwareInterface
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
        $countries = $this->container->getParameter('mfb_country.default.countries');
        foreach ($countries as $country) {
            $countryEntity = $this->createNewCountryEntity($country);
            $manager->persist($countryEntity);
        }
        $manager->flush();
    }

    /**
     * @param $country
     * @return Country
     */
    private function createNewCountryEntity($country)
    {
        $countryEntity = new Country();
        $countryEntity->setName($country);
        return $countryEntity;
    }

}