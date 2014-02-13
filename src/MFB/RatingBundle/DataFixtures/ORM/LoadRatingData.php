<?php
namespace MFB\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\RatingBundle\Entity\Rating;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadRatingData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    private $ratingNames = array(
        'Zuverlässigkeit',
        'Preis/Leistung',
        'Pünktlichkeit/Schnelligkeit',
        'Qualität',
        'Freundlichkeit',
        'Kompetenz',
        'Auswahl/Sortiment'
    );

    public function load(ObjectManager $manager)
    {
        foreach ($this->ratingNames as $ratingName) {
            $ratingEntity = $this->createNewRatingEntity($ratingName);
            $manager->persist($ratingEntity);
            $this->addReference("rating-{$ratingName}", $ratingEntity);
        }
        $manager->flush();
    }

    private function createNewRatingEntity($ratingName)
    {
        $ratingEntity = new Rating();
        $ratingEntity->setName($ratingName);
        $ratingEntity->setIsCustom(false);
        return $ratingEntity;
    }

    public function getOrder()
    {
        return 1;
    }

}
