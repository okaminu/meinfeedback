<?php
namespace MFB\SetupWizardBundle;

interface WizardStepInterface
{
    public function getPriority();
    public function getRoute();
    public function getName();
}