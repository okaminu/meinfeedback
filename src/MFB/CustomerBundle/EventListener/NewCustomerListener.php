<?php


namespace MFB\CustomerBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use MFB\CustomerBundle\Event\NewCustomerEvent;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Service\Sender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\FeedbackBundle\Entity\FeedbackInvite;

class NewCustomerListener
{

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Sender
     */
    private $sender;

    private $translator;

    private $router;

    public function __construct(ObjectManager $em, Sender $sender, Translator $translator, $router)
    {
        $this->em = $em;

        $this->sender = $sender;

        $this->translator = $translator;

        $this->router = $router;
    }

    public function onCreateCustomerComplete(NewCustomerEvent $event)
    {
        $accountId = $event->getChannel()->getAccountId();
        $customerId = $event->getCustomer()->getId();
        $accountChannelId = $event->getChannel()->getId();

        $invite = $this->createFeedbackInvite($accountId, $customerId, $accountChannelId);

        $inviteUrl = $this->getFeedbackInviteUrl($invite);

        $accountId = $event->getCustomer()->getAccountId();

        $emailTemplate = $this->getEmailTemplate($accountId);

        $this->sender->createForAccountChannel(
            $emailTemplate,
            $event->getChannel(),
            $event->getCustomer(),
            $event->getService(),
            $inviteUrl
        );
    }

    /**
     * @param $accountId
     * @return EmailTemplate
     */
    private function getEmailTemplate($accountId)
    {
        $emailTemplate = $this->em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => 'AccountChannel',
            )
        );

        //set defaults values
        if (!$emailTemplate) {
            $emailTemplate = new EmailTemplate();
            $emailTemplate->setTitle($this->translator->trans('Please leave feedback'));
            $emailTemplate->setTemplateCode(
                $this->translator->trans('default_account_channel_template')
            );
        }
        return $emailTemplate;
    }

    /**
     * @param $accountId
     * @param $customerId
     * @param $accountChannelId
     * @return FeedbackInvite
     */
    private function createFeedbackInvite($accountId, $customerId, $accountChannelId)
    {
        $invite = new FeedbackInvite();
        $invite->setAccountId($accountId);
        $invite->setCustomerId($customerId);
        $invite->setChannelId($accountChannelId);
        $invite->updatedTimestamps();
        $this->em->persist($invite);
        $this->em->flush();
        return $invite;
    }

    /**
     * @param $invite
     * @return mixed
     */
    private function getFeedbackInviteUrl($invite)
    {
        $inviteUrl = $this->router->generate(
            'mfb_feedback_create_with_invite',
            array('token' => $invite->getToken()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $inviteUrl;
    }
}
