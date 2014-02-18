<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\AdminBundle\Service\FormSetupSteps;
use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class Configurator
{
    private $adminFormSetupSteps;

    public function __construct(FormSetupSteps $adminFormSetupSteps)
    {
        $this->adminFormSetupSteps = $adminFormSetupSteps;
    }

    public function configure(WizardStepsAwareInterface $service)
    {
        $service->setSteps($this->adminFormSetupSteps->getSteps());
        $service->setAfterSetup($this->adminFormSetupSteps->getAfterSetup());
    }
}
