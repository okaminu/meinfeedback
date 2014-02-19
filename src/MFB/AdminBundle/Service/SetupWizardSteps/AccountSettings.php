<?php
namespace MFB\AdminBundle\Service\SetupWizardSteps;

use MFB\SetupWizardBundle\Event\StepEvent;
use MFB\SetupWizardBundle\Service\WizardStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepInterface;

class AccountSettings implements WizardStepInterface, EventSubscriberInterface
{
    private $priority = 600;

    private static $name = 'AccountSettings';

    private $route = 'mfb_admin_setup_account_settings';

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
        $this->stepService->setStepStatus($event->getChannelId(), 'AccountSettings', 'complete');
        $this->stepService->setStepStatus($event->getChannelId(), 'Finished', 'pending');

    }
}