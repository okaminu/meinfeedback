<?php
namespace MFB\SetupWizardBundle;

class StepsCollection
{
    private $steps;

    public function getStepsArray()
    {
        return $this->steps;
    }

    public function addStep(WizardStepInterface $step)
    {
        $this->steps[] = $step;
    }

    public function sortByPriority()
    {
        usort($this->steps, array('MFB\SetupWizardBundle\StepsCollection', 'compareSteps'));
    }

    public function getStepsByNames($names)
    {
        $selection = array();
        foreach ($this->steps as $step) {
            if (in_array($step->getName(), $names)) {
                $selection[] = $step;
            }
        }
        return $selection;
    }

    public static function compareSteps($stepA, $stepB)
    {
        return $stepA->getPriority() > $stepB->getPriority();
    }
}
