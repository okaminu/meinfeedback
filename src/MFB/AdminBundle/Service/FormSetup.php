<?php
namespace MFB\AdminBundle\Service;

use MFB\ChannelBundle\Service\Channel;
use MFB\ChannelBundle\Service\ChannelRatingCriteria as CriteriaService;
use MFB\ChannelBundle\Service\ChannelServiceDefinition;
use MFB\ChannelBundle\Service\ChannelServiceType;
use MFB\ServiceBundle\Service\ServiceDefinition;
use MFB\ServiceBundle\Service\ServiceProvider;
use Symfony\Component\Security\Core\SecurityContext;

class FormSetup
{
    private $ratingCriteriaService;

    private $serviceType;

    private $channelService;

    private $definitionService;

    private $providerService;
    
    public function __construct(
        CriteriaService $ratingCriteriaService,
        ChannelServiceType $serviceType,
        Channel $channelService,
        ChannelServiceDefinition $definitionService,
        ServiceProvider $providerService
    ) {
        $this->ratingCriteriaService = $ratingCriteriaService;
        $this->serviceType = $serviceType;
        $this->channelService = $channelService;
        $this->definitionService = $definitionService;
        $this->providerService = $providerService;
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

    public function removeAllSetupFormData($accountId)
    {
        $channel = $this->channelService->findByAccountId($accountId);
        if ($channel == null) {
            return 0;
        }

        $this->removeDefinitions($channel->getId());
        $this->removeChannelCriterias($channel->getId());
        $this->removeServiceProviders($channel->getId());
        $this->removeServiceTypes($channel->getId());

        $this->channelService->remove($channel);

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
        $missingCount = $this->ratingCriteriaService->missingCount($channelId);
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

    private function removeDefinitions($channelId)
    {
        $definitions = $this->definitionService->findByChannelId($channelId);
        $this->definitionService->removeList($definitions);
    }

    private function removeChannelCriterias($channelId)
    {
        $criterias = $this->ratingCriteriaService->findByChannelId($channelId);
        $this->ratingCriteriaService->removeList($criterias);
    }

    private function removeServiceProviders($channelId)
    {
        $providers = $this->providerService->findByChannelId($channelId);
        $this->providerService->removeList($providers);
    }

    private function removeServiceTypes($channelId)
    {
        $serviceTypes = $this->serviceType->findByChannelId($channelId);
        $this->serviceType->removeList($serviceTypes);
    }
}
