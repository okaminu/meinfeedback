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
    private $criteriaLimit;


    public function __construct(EntityManager $em, $criteriaLimit)
    {
        $this->entityManager = $em;
        $this->criteriaLimit = $criteriaLimit;
    }

    public function createNewChannelCriteria($channel)
    {
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

    public function hasSelectedRatingCriterias($accountId)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();

        $usedCriteriaCount = $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->getUsedCriteriaCount($accountChannelId);
        
        if ($usedCriteriaCount < $this->criteriaLimit) {
            return false;
        }
        return true;
    }


    public function getNotUsedRatingCriterias($channelId)
    {
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->findAllUnusedRatingCriterias($channelId);
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
