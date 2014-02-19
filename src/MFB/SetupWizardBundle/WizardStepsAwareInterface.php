<?php
namespace MFB\SetupWizardBundle;

interface WizardStepsAwareInterface
{
    public function getPriority();
    public function getRoute();
}