<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\Event\NewFeedbackInviteEvent;
use MFB\ServiceBundle\Service\Service;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;
use MFB\FeedbackBundle\Entity\FeedbackInvite as FeedbackInviteEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;


class FeedbackInvite
{
    private $entityManager;

    private $service;

    private $eventDispatcher;

    private $router;


    public function __construct(EntityManager $em, Service $service, EventDispatcher $ed, Router $router)
    {
        $this->entityManager = $em;
        $this->service = $service;
        $this->eventDispatcher = $ed;
        $this->router = $router;
    }

    public function createNewFeedbackInvite($accountId, $customerId, ServiceEntity $service = null)
    {
        if (!$service) {
            $service = $this->service->createNewService($accountId, $this->getExistingCustomer($customerId));
        }

        $invite = $this->createNewFeedbackInviteEntity(
            $accountId,
            $customerId,
            $this->getAccountChannel($accountId)->getId(),
            $service
        );

        return $invite;
    }


    public function store($invite)
    {
        $this->eventDispatcher->dispatch(FeedbackEvents::INVITE_SEND_INITIALIZE);
        $this->saveEntity($invite);
        $this->dispatchCreateFeedbackInviteSendEvent($invite);
    }

    /**
     * @param $entity
     */
    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function processInviteFeedback($invite, $feedback)
    {
        $this->eventDispatcher->dispatch(FeedbackEvents::INVITE_INITIALIZE);
        $this->remove($invite);
        $this->dispatchCreateFeedbackInviteWroteEvent($invite, $feedback);

    }

    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getAccountChannel($accountId)
    {
        $accountChannel = $this->entityManager->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        return $accountChannel;
    }

    private function getExistingCustomer($customerId)
    {
        $customer = $this->entityManager->getRepository('MFBCustomerBundle:Customer')->findOneBy(
            array('id' => $customerId)
        );
        return $customer;
    }

    /**
     * @param $invite
     */
    private function dispatchCreateFeedbackInviteSendEvent(FeedbackInviteEntity $invite)
    {
        $accountChannel = $this->getAccountChannel($invite->getAccountId());
        $service = $invite->getService();
        $inviteUrl = $this->getFeedbackInviteUrl($invite);
        $event = new NewFeedbackInviteEvent($service->getCustomer(), $accountChannel, $service, $inviteUrl);
        $this->eventDispatcher->dispatch(FeedbackEvents::INVITE_SEND_COMPLETE, $event);
    }

    /**
     * @param $accountId
     * @param $customerId
     * @param $accountChannelId
     * @param $service
     * @return FeedbackInviteEntity
     */
    private function createNewFeedbackInviteEntity($accountId, $customerId, $accountChannelId, $service)
    {
        $invite = new FeedbackInviteEntity();
        $invite->setAccountId($accountId);
        $invite->setCustomerId($customerId);
        $invite->setChannelId($accountChannelId);
        $invite->setService($service);
        $invite->updatedTimestamps();
        return $invite;
    }

    private function getAccount($accountId)
    {
        $account = $this->entityManager->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('id' => $accountId)
        );
        return $account;
    }

    private function getFeedbackInviteUrl($invite)
    {
        $inviteUrl = $this->router->generate(
            'mfb_feedback_create_with_invite',
            array('token' => $invite->getToken()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $inviteUrl;
    }

    /**
     * @param $feedback
     * @param $invite
     */
    private function dispatchCreateFeedbackInviteWroteEvent($invite, $feedback)
    {
        $customer = $feedback->getCustomer();
        $account = $this->getAccount($feedback->getAccountId());

        $event = new CustomerAccountEvent(
            $feedback->getId(),
            $account->getEmail(),
            $customer,
            $feedback->getContent(),
            $feedback->getRating(),
            $invite
        );
        $this->eventDispatcher->dispatch(FeedbackEvents::INVITE_COMPLETE, $event);
    }
}