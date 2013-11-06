<?php
namespace MFB\EmailBundle\Service;


use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
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
        $entity
    ) {
        $message = new \Swift_Message();
        $message_title = $template->getTitle();

        $message->setFrom(array('mazvydas@meinfeedback.net' => 'MeinFeedback.net'));
        $message->setTo($customer->getEmail());
        $message->setSubject($message_title);

        $emailContent = $template->getTemplateCode();
        $emailContent = $this->replacePlaceHolders(
            $emailContent,
            array(
                  "#LINK#" => $inviteUrl,
                  "#FIRSTNAME#" => $entity->getFirstName(),
                  "#LASTNAME#" => $entity->getLastName(),
                  "#SAL#" => $entity->getSalutation(),
                  "#SERVICE_DATE#" => $entity->getServiceDate()->format('Y-m-d'),
                  "#REFERENCE_ID#" => $entity->getReferenceId(),
                  "#SERVICE_NAME#" => $entity->getServiceName(),
                  "#HOMEPAGE#" => $entity->getHomepage()
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