<?php
namespace MFB\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\RatingBundle\Entity\Rating;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRatingData implements FixtureInterface, ContainerAwareInterface
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
        $ratingNames = $this->container->getParameter('mfb_rating.default.ratings');
        foreach ($ratingNames as $ratingName) {
            $ratingEntity = $this->createNewRatingEntity($ratingName);
            $manager->persist($ratingEntity);
        }
        $manager->flush();
    }

    private function createNewRatingEntity($ratingName)
    {
        $ratingEntity = new Rating();
        $ratingEntity->setName($ratingName);
        return $ratingEntity;
    }
}
