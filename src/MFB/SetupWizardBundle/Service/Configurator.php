<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\AdminBundle\Service\FormSetupSteps;
use MFB\SetupWizardBundle\StepsCollection;
use MFB\SetupWizardBundle\WizardStepInterface;

class Configurator
{
    private $stepsCollection;

    private $eventDispatcher;

    private $stepService;

    public function __construct($ed, WizardStep $stepService)
    {
        $this->eventDispatcher = $ed;
        $this->stepService = $stepService;
        $this->stepsCollection = new StepsCollection();
    }

    public function addStep(WizardStepInterface $step)
    {
        $this->eventDispatcher->addSubscriber($step);
        $this->stepsCollection->addStep($step);
    }

    public function getStepsCollection()
    {
        return $this->stepsCollection;
    }
}
