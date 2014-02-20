<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Event\StepEvent;
use MFB\SetupWizardBundle\Service\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class ServiceType implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 200;

    private static $name = 'ServiceType';

    private $route = 'mfb_admin_setup_select_service';

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
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceType', 'complete');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceDefinition', 'pending');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceCriterias', 'pending');
    }
}