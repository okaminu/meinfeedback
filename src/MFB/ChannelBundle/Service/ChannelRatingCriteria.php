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
    private $channelServiceType;
    private $channelService;

    public function __construct(
        EntityManager $em,
        $criteriaLimit,
        ChannelServiceType $channelServiceType,
        Channel $channelService
    ) {
        $this->entityManager = $em;
        $this->criteriaLimit = $criteriaLimit;
        $this->channelServiceType = $channelServiceType;
        $this->channelService = $channelService;
    }

    public function createNew($channel)
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

    public function getNotUsed($channelId)
    {
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->findAllUnusedRatingCriterias($channelId);
    }

    public function getNotUsedCriteriasForService($channelId)
    {
        $channelServices = $this->channelServiceType->findByChannelId($channelId);

        $serviceIds = array();
        foreach ($channelServices as $service) {
            $serviceIds[] = $service->getServiceType()->getId();
        }
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->findAllUnusedCriteriasForServices($channelId, $serviceIds);
    }

    public function getUsedCount($channelId)
    {
        $accountChannelId = $this->channelService->findById($channelId)->getId();
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->getUsedCriteriaCount($accountChannelId);
    }

    public function missingCount($channelId)
    {
        $usedCriteriaCount = $this->getUsedCount($channelId);
        return $this->criteriaLimit - $usedCriteriaCount;
    }

    public function findByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')->findBy(
            array('channel' => $channelId)
        );
    }

    public function removeList($list)
    {
        try {
            foreach ($list as $single) {
                $this->entityManager->remove($single);
            }
            $this->entityManager->flush();
        } catch (DBALException $ex) {
            throw new  ChannelException('Cannot remove channel rating criteria');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
