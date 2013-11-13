<?php
namespace MFB\EmailBundle\Service;


use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\ServiceBundle\Entity\Service;
use MFB\EmailBundle\Entity\EmailTemplate;
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

    public function createForAccountChannel(
        Customer $customer,
        AccountChannel $channel,
        EmailTemplate $template,
        $inviteUrl,
        Service $service
    ) {
        $message = new \Swift_Message();
        $message_title = $template->getTitle();

        $message->setFrom(array('mazvydas@meinfeedback.net' => 'MeinFeedback.net'));
        $message->setTo($customer->getEmail());
        $message->setSubject($message_title);

        $emailContent = $template->getTemplateCode();
        $date = null;
        if ($service->getDate()) {
            $date = $service->getDate()->format('Y-m-d');
        }
        $emailContent = $this->replacePlaceHolders(
            $emailContent,
            array(
                  "#LINK#" => $inviteUrl,
                  "#FIRSTNAME#" => $customer->getFirstName(),
                  "#LASTNAME#" => $customer->getLastName(),
                  "#SAL#" => $customer->getSalutation(),
                  "#SERVICE_DATE#" => $date,
                  "#REFERENCE_ID#" => $service->getServiceIdReference(),
                  "#SERVICE_NAME#" => $service->getDescription(),
                  "#HOMEPAGE#" => $customer->getHomepage()
            )
        );

        $emailBody = $this->twig->render(
            'MFBEmailBundle:Default:AccountChannelEmail.html.twig',
            array(
                'email_title' => $message_title,
                'email_content' => $emailContent,
                'account_channel_name' => $channel->getName(),
                'create_feedback_link' => $inviteUrl
            )
        );
        $message->setBody(
            $emailBody,
            'text/html'
        );
        $this->mailer->send($message);
    }

    public function replacePlaceHolders($html, $placeholders)
    {
        if (isset($placeholders['#LINK#'])) {
            $placeholders['#LINK#'] = '<a href="' . $placeholders['#LINK#'] . '">' . $placeholders['#LINK#'] . '</a>';
        }

        $html = strtr($html, $placeholders);
        return $html;
    }
}