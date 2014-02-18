<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class SetupWizard implements WizardStepsAwareInterface
{
    private $setupSteps;

    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setSteps($steps)
    {
        $this->setupSteps = $steps;
    }

}
