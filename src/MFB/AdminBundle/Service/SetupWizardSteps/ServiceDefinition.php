<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Event\StepEvent;
use MFB\SetupWizardBundle\Service\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class ServiceDefinition implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 300;

    private static $name = 'ServiceDefinition';

    private $route = 'mfb_admin_setup_insert_definitions';

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

    public function afterStep(StepEvent $event)
    {
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceDefinition', 'complete');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceCriterias', 'pending');
    }
}