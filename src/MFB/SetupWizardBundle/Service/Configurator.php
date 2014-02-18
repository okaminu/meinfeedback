<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\AdminBundle\Service\FormSetupSteps;
use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class Configurator
{
    private $stepsConfig = array();

    private $eventDispatcher;

    public function __construct($ed)
    {
        $this->eventDispatcher = $ed;
    }

    public function addStep(WizardStepsAwareInterface $service)
    {
        $this->eventDispatcher->addSubscriber($service->getSubscribedEvents());
        $this->stepsConfig[$service->getPriority()] =
            array(
                'route' => $service->getRoute(),
                'name' => get_class($service)
            );
    }

    public function getStepsConfig()
    {
        return $this->stepsConfig;
    }
}
