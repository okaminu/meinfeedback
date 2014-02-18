<?php
namespace MFB\AdminBundle\Service;

class FormSetupSteps
{
    private $steps;
    private $afterSetup;

    public function __construct($steps, $afterSetup)
    {
        $this->steps = $steps;
        $this->afterSetup = $afterSetup;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function getAfterSetup()
    {
        return $this->afterSetup;
    }

}
