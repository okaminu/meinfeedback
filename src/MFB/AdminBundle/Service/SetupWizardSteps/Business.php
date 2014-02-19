<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Event\StepEvent;
use MFB\SetupWizardBundle\Service\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class Business implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 100;

    private static $name = 'Business';

    private $route = 'mfb_admin_setup_select_business';

    private $stepService;

    public function __construct(WizardStep $stepService)
    {
        $this->stepService = $stepService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            "setupWizard.after". self::$name =>
            array('afterStep'),
            "setupWizard.before". self::$name =>
            array('beforeStep')
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

    public function beforeStep(StepEvent $event)
    {
        $this->stepService->setStepStatus($event->getChannelId(), 'Business', 'pending');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceType', 'inactive');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceDefinition', 'inactive');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceCriterias', 'inactive');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceProvider', 'inactive');
        $this->stepService->setStepStatus($event->getChannelId(), 'Finished', 'inactive');
    }

    public function afterStep(StepEvent $event)
    {
        $this->stepService->setStepStatus($event->getChannelId(), 'Business', 'complete');
        $this->stepService->setStepStatus($event->getChannelId(), 'ServiceType', 'pending');
    }
}