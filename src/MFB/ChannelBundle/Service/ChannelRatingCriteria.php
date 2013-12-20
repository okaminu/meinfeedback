<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Entity\ChannelRatingCriteria as ChannelRatingCriteriaEntity;

class ChannelRatingCriteria
{
    private $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function createNewChannelCriteria($accountId)
    {
        $channel = $this->getAccountChannel($accountId);
        $ratingCriteria = new ChannelRatingCriteriaEntity();
        $ratingCriteria->setChannel($channel);
        return $ratingCriteria;
    }

    public function store($channel)
    {
        try {
            $this->saveEntity($channel);
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot store channel data');
        }
    }

    private function getAccountChannel($accountId)
    {
        $accountChannel = $this->entityManager->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        return $accountChannel;
    }

    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
