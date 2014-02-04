<?php
namespace MFB\AdminBundle\Service;

use MFB\ChannelBundle\Service\Channel;
use MFB\ChannelBundle\Service\ChannelRatingCriteria as CriteriaService;
use MFB\ChannelBundle\Service\ChannelServiceType;
use Symfony\Component\Security\Core\SecurityContext;

class FormSetup
{
    private $ratingCriteriaService;

    private $serviceType;

    private $channelService;
    
    public function __construct(
        CriteriaService $ratingCriteriaService,
        ChannelServiceType $serviceType,
        Channel $channelService
    ) {
        $this->ratingCriteriaService = $ratingCriteriaService;
        $this->serviceType = $serviceType;
        $this->channelService = $channelService;
    }

    public function isMissingMandatorySettings($accountId)
    {
        $channel = $this->channelService->findByAccountId($accountId);
        if ($channel == null) {
            return true;
        }

        if ($this->hasSelectedRatingCriterias($channel->getId()) &&
            $this->hasVisibleServiceType($channel->getId()) &&
            $this->hasChannelInfo($channel)
        ) {
            return false;
        }

        return true;
    }

    private function hasChannelInfo($channel)
    {
        if (!$channel->getName() ||
            !$channel->getStreet() ||
            !$channel->getPlace() ||
            !$channel->getCity() ||
            !$channel->getHomepageUrl() ||
            !$channel->getBusiness()) {
            return false;
        }
        return true;
    }

    private function hasSelectedRatingCriterias($channelId)
    {
        $missingCount = $this->ratingCriteriaService->missingRatingCriteriaCount($channelId);
        if ($missingCount > 0) {
            return false;
        }
        return true;
    }


    private function hasVisibleServiceType($channelId)
    {
        $service = $this->serviceType->findVisibleByChannelId($channelId);
        if (count($service) > 0) {
            return true;
        }
        return false;
    }

}
