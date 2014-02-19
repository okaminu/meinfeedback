<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class Business implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 100;

    private static $name = 'Business';

    private $route = 'mfb_admin_setup_select_business';

    public static function getSubscribedEvents()
    {
        return array(
            "setupWizard.post". self::$name =>
            array('postStep')
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

    public function postStep($event)
    {
        echo 'test';
    }
}