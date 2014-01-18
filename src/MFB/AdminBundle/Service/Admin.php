<?php
namespace MFB\AdminBundle\Service;

use MFB\ChannelBundle\Service\ChannelRatingCriteria as CriteriaService;
use MFB\ServiceBundle\Service\ServiceGroup;
use MFB\ServiceBundle\Service\ServiceProvider;
use Symfony\Component\Security\Core\SecurityContext;

class Admin
{
    private $ratingCriteriaService;

    private $serviceProvider;

    private $serviceGroup;
    
    private $securityContext;

    public function __construct(
        CriteriaService $ratingCriteriaService,
        ServiceGroup $serviceGroup,
        ServiceProvider $serviceProvider,
        SecurityContext $securityContext
    ) {
        $this->ratingCriteriaService = $ratingCriteriaService;
        $this->serviceGroup = $serviceGroup;
        $this->serviceProvider = $serviceProvider;
        $this->securityContext = $securityContext;
    }

    public function missingMandatorySettingsErrors($accountId)
    {
        $errors = array();
        if ($this->isUserMissingCriterias($accountId)) {
            $count = $this->ratingCriteriaService->missingRatingCriteriaCount($accountId);
            $errors[] = "Please insert {$count} rating criterias";
        }
        if ($this->isUserMissingServiceGroup($accountId)) {
            $errors[] = 'Please insert at least one visible Service Type';
        }

        if ($this->isUserMissingServiceProvider($accountId)) {
            $errors[] = 'Please insert at least one visible Employee';
        }
        return $errors;
    }

    public function isMissingMandatorySettings($accountId)
    {
        if (count($this->missingMandatorySettingsErrors($accountId)) > 0) {
            return true;
        }
        return false;
    }

    private function isUserMissingCriterias($accountId)
    {
        return !$this->ratingCriteriaService->hasSelectedRatingCriterias($accountId);
    }

    private function isUserMissingServiceProvider($accountId)
    {
        return !$this->serviceProvider->hasVisibleServiceProviders($accountId);
    }

    private function isUserMissingServiceGroup($accountId)
    {
        return !$this->serviceGroup->hasVisibleServiceGroup($accountId);
    }
}