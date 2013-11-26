<?php
namespace MFB\EmailBundle\Service;


use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\ServiceBundle\Entity\Service;
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
                  "#HOMEPAGE#" => $channel->getHomepageUrl(),
                  "#SERVICE_ID#" => $service->getServiceIdReference(),
                  "#CUSTOMER_ID#" => $customer->getCustomerIdReference()
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

    public function sendFeedbackNotification(Account $account, Customer $customer, $feedbackText, $feedbackRating, $feedbackEnableLink)
    {
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
                'feedbackRating' => $feedbackRating,
                'enabaleFeedbackLink' => $feedbackEnableLink
            )
        );
        $this->sendEmail($account->getEmail(), $emailSubject, $emailBody);
    }

    /**
     * Send an email with password reset link
     * @param Account $account
     */
    public function sendResettingEmailMessage(Account $account)
    {
        $url = $this->router->generate(
            'mfb_account_reset',
            array('token' => $account->getResetToken()),
            true
        );

        $emailSubject = $this->translator->trans('Password reset link');

        $rendered = $this->twig->render(
            "MFBEmailBundle:Default:ResetPasswordEmail.html.twig",
            array(
                'email_title' => $emailSubject,
                'account' => $account,
                'confirmationUrl' => $url
            )
        );

        $this->sendEmail($account->getEmail(), $emailSubject, $rendered);
    }
}