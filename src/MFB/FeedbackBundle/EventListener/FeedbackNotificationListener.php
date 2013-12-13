<?php

namespace MFB\FeedbackBundle\EventListener;

use MFB\EmailBundle\Service\Sender;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FeedbackNotificationListener
{
    private $sender;

    private $router;

    public function __construct(Router $router, Sender $sender)
    {
        $this->router = $router;
        $this->sender = $sender;
    }

    public function onRegularComplete(CustomerAccountEvent $event)
    {
        $this->sender->sendFeedbackNotification(
            $event->getEmail(),
            $event->getCustomer(),
            $event->getFeedbackText(),
            $event->getFeedbackRating(),
            $this->router->generate(
                'mfb_feedback_enable',
                array('feedbackId' => $event->getFeedbackId()),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $event->getInvite()
        );

    }


}
