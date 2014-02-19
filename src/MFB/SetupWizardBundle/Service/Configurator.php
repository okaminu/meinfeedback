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
        $reflection = new \ReflectionClass(get_class($service));

        $this->eventDispatcher->addSubscriber($service);
        $this->stepsConfig[$service->getPriority()] =
            array(
                'route' => $service->getRoute(),
                'name' => $reflection->getShortName()
            );
    }

    public function getStepsConfig()
    {
        return $this->stepsConfig;
    }
}
