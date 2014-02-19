<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Service\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class ServiceProvider implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 500;

    private static $name = 'ServiceProvider';

    private $route = 'mfb_admin_setup_insert_service_provider';

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