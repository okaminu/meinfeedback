<?php
namespace MFB\ChannelBundle\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class BusinessWizardStep implements WizardStepsAwareInterface, EventSubscriberInterface
{
    public function getPriority()
    {
        return 100;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'setupWizard.postBusinessWizardStep' =>
            array('postBusinessWizardStep')
        );
    }

    public function getRoute()
    {
        return 'mfb_admin_setup_select_business';
    }

    public function postBusinessWizardStep($event)
    {
        echo 'test';
    }
}