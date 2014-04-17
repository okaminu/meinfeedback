<?php


namespace MFB\FeedbackBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Service\Sender;
use MFB\FeedbackBundle\Event\NewFeedbackInviteEvent;
use Symfony\Component\Translation\Translator;

class NewFeedbackInviteListener
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

    public function __construct(ObjectManager $em, Sender $sender, Translator $translator)
    {
        $this->em = $em;

        $this->sender = $sender;

        $this->translator = $translator;
    }

    public function onCreateFeedbackInviteComplete(NewFeedbackInviteEvent $event)
    {
        $accountId = $event->getCustomer()->getAccountId();

        $emailTemplate = $this->getEmailTemplate($accountId);

        $this->sender->createForAccountChannel(
            $emailTemplate,
            $event->getChannel(),
            $event->getCustomer(),
            $event->getService(),
            $event->getInviteUrl()
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
}
