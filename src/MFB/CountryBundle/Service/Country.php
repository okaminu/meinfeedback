<?php
namespace MFB\CountryBundle\Service;

use Doctrine\ORM\EntityManager;

class Country
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function findByName($countryName)
    {
        $result = $this->entityManager->getRepository('MFBCountryBundle:Country')->findOneBy(
            array(
                'name' => $countryName
            )
        );
        return $result;
    }

    public function findAll()
    {
        return $this->entityManager->getRepository('MFBCountryBundle:Country')->findAll();
    }
}
