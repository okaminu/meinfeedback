<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Entity\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class Finished implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 700;

    private static $name = 'Finished';

    private $route = 'mfb_admin_homepage';

    private $stepService;

    public function __construct(WizardStep $stepService)
    {
        $this->stepService = $stepService;
    }

    public static function getSubscribedEvents()
    {

    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getName()
    {
        return self::$name;
    }

}