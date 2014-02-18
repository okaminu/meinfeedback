<?php
namespace MFB\SetupWizardBundle;

interface WizardStepsAwareInterface
{
    public function setSteps($steps);
    public function setAfterSetup($route);
}