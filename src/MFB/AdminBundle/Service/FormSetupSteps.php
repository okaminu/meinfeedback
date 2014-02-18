<?php
namespace MFB\AdminBundle\Service;

class FormSetupSteps
{
    private $steps;
    
    public function __construct($steps)
    {
        $this->steps = $steps;
    }

    public function getSteps()
    {
        return $this->steps;
    }

}
