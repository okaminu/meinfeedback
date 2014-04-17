<?php
namespace MFB\EmailBundle\Service;


use MFB\AccountBundle\Entity\Account;
use MFB\CustomerBundle\Entity\Customer;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\RouterInterface;


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

    public function __construct(\Swift_Mailer $mailer, EngineInterface $twig, RouterInterface $router, Translator $translator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function createForAccountChannel(EmailTemplate $template, $channel, $customer, $service, $inviteUrl)
    {
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
                  "#SERVICE_NAME#" => $service->getServiceType()->getName(),
                  "#HOMEPAGE#" => $channel->getHomepageUrl(),
                  "#SERVICE_ID#" => $service->getServiceIdReference(),
                  "#CUSTOMER_ID#" => $customer->getCustomerIdReference(),
                  "#EMAIL#" => $customer->getEmail()
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

    public function sendEmail($destinationEmail, $emailSubject, $emailText)
    {
        $message = new \Swift_Message();
        $message->setFrom(array('mazvydas@meinfeedback.net' => 'MeinFeedback.net'));
        $message->setTo($destinationEmail);
        $message->setSubject($emailSubject);
        $message->setBody($emailText, 'text/html');
        $this->mailer->send($message);
    }

    public function sendFeedbackNotification(
        $email,
        Customer $customer,
        $feedbackText,
        $feedbackRatingCriterias,
        $feedbackEnableLink,
        FeedbackInvite $invite = null
    ) {
        $emailSubject = $this->translator->trans('Feedback received on meinfeedback');
        $customerName = $customer->getEmail();

        if (($customer->getFirstName()) && ($customer->getLastName())) {
            $customerName = $customer->getFirstName() ." ". $customer->getLastName();
        }

        $emailBody = $this->twig->render(
            'MFBEmailBundle:Default:FeedbackNotificationEmail.html.twig',
            array(
                'email_title' => $emailSubject,
                'customerName' => $customerName,
                'feedbackText' => $feedbackText,
                'feedbackRatingCriterias' => $feedbackRatingCriterias,
                'enabaleFeedbackLink' => $feedbackEnableLink,
                'invite' => $invite
            )
        );
        $this->sendEmail($email, $emailSubject, $emailBody);
    }

    /**
     * Send email with new password
     *
     * @param Account $account
     * @param $newPassword
     * @throws \ErrorException
     */
    public function sendResettingEmailMessage(Account $account, $newPassword)
    {
        if (!$newPassword) {
            throw new \ErrorException('New password not set!');
        }

        $emailSubject = $this->translator->trans('Your new password');

        $rendered = $this->twig->render(
            "MFBEmailBundle:Default:ResetSendPasswordEmail.html.twig",
            array(
                'email_title' => $emailSubject,
                'account' => $account,
                'new_password' => $newPassword
            )
        );

        $this->sendEmail($account->getEmail(), $emailSubject, $rendered);
    }
}