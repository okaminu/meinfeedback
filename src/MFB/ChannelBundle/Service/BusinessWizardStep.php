<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\Common\EventSubscriber;
use MFB\SetupWizardBundle\WizardStepsAwareInterface;

class BusinessWizardStep implements WizardStepsAwareInterface, EventSubscriber
{
    public function getPriority()
    {
        return 100;
    }

    public function getSubscribedEvents()
    {
        return array('preBusinessWizardStep', 'postBusinessWizardStep');
    }

    public function getRoute()
    {
        return 'mfb_admin_setup_select_business';
    }
}