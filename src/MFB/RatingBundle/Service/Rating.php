<?php
namespace MFB\RatingBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\RatingBundle\Entity\Rating as RatingEntity;
use MFB\RatingBundle\RatingException;

class Rating
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewRating()
    {
        return new RatingEntity();
    }

    public function createCustom($name)
    {
        $entity = $this->createNewRating();
        $entity->setIsCustom(true);
        $entity->setName($name);
        return $entity;
    }

    public function store($rating)
    {
        try {
            $this->saveEntity($rating);
        } catch (DBALException $ex) {
            throw new RatingException('Cannot save rating criteria');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
