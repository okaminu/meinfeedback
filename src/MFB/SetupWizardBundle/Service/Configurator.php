<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\AdminBundle\Service\FormSetupSteps;
use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class Configurator
{
    private $stepsConfig = array();

    public function addStep(WizardStepsAwareInterface $service)
    {
        $this->stepsConfig[$service->getPriority()] =
            array(
                'route' => $service->getRoute(),
                'events' => $service->getSubscribedEvents()
            );
    }

    public function getStepsConfig()
    {
        return $this->stepsConfig;
    }
}
