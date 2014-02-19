<?php


namespace MFB\SetupWizardBundle\Event;

use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback;
use Symfony\Component\EventDispatcher\Event;

class StepEvent extends Event
{
    private $channelId;

    public function __construct($channelId)
    {
        $this->channelId = $channelId;
    }

    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }

    public function getChannelId()
    {
        return $this->channelId;
    }
}
