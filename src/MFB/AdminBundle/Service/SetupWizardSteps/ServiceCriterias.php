<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Entity\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class ServiceCriterias implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 400;

    private static $name = 'ServiceCriterias';

    private $route = 'mfb_admin_setup_select_criterias';

    private $stepService;

    public function __construct(WizardStep $stepService)
    {
        $this->stepService = $stepService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            "setupWizard.after". self::$name =>
            array('afterStep')
        );
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

    public function afterStep($event)
    {
        echo 'test';
    }
}