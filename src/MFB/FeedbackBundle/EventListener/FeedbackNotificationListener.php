<?php

namespace MFB\FeedbackBundle\EventListener;

use MFB\EmailBundle\Service\Sender;
use MFB\FeedbackBundle\Event\FeedbackNotificationEvent;
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

    public function onRegularComplete(FeedbackNotificationEvent $event)
    {
        /**
         * @var $feedback \MFB\FeedbackBundle\Entity\Feedback
         */
        $feedback = $event->getFeedback();
        $this->sender->sendFeedbackNotification(
            $event->getEmail(),
            $event->getCustomer(),
            $feedback->getContent(),
            $feedback->getFeedbackRating(),
            $this->router->generate(
                'mfb_feedback_enable',
                array('feedbackId' => $feedback->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $event->getInvite()
        );

    }


}
