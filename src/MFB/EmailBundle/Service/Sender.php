<?php
namespace MFB\EmailBundle\Service;


use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use Symfony\Bundle\TwigBundle\TwigEngine;


class Sender
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var TwigEngine
     */
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;

    }

    public function createForAccountChannel(Customer $customer, AccountChannel $channel)
    {
        $message = new \Swift_Message();
        $message_title = 'Please leave feedback for '.$channel->getName();

        $message->setFrom( array( 'info@meinfeedback.net' => 'MeinFeedback.net' ) );
        $message->setTo($customer->getEmail());
        $message->setSubject($message_title);

        $message->setBody(
            $this->twig->render(
                'MFBEmailBundle:Default:AccountChannelEmail.html.twig',
                array(
                    'email_title' => $message_title,
                    'account_channel_name' => $channel->getName(),
                    'create_feedback_link' => 'http://www.meinfeedback.net/'
                )
            ),
            'text/html'
        );
        $this->mailer->send($message);
    }
}