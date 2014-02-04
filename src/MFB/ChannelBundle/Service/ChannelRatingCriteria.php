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

    public function getNotUsedRatingCriterias($channelId)
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

    public function getUsedRatingCriteriasCount($channelId)
    {
        $accountChannelId = $this->channelService->findById($channelId)->getId();
        return $this->entityManager->getRepository('MFBChannelBundle:ChannelRatingCriteria')
            ->getUsedCriteriaCount($accountChannelId);
    }

    public function missingRatingCriteriaCount($channelId)
    {
        $usedCriteriaCount = $this->getUsedRatingCriteriasCount($channelId);
        return $this->criteriaLimit - $usedCriteriaCount;
    }


    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
