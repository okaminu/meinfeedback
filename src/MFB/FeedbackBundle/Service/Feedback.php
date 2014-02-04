<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Service\Account;
use MFB\ChannelBundle\Service\Channel;
use MFB\FeedbackBundle\Service\FeedbackRating as FeedbackRatingService;
use MFB\FeedbackBundle\Event\FeedbackNotificationEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\ServiceBundle\Service\Service;
use MFB\CustomerBundle\Service\Customer as CustomerService;

class Feedback
{
    private $entityManager;

    private $customerService;

    private $service;

    private $eventDispatcher;

    private $serviceEntity = null;
    
    private $customerEntity = null;
    
    private $feedbackRatingService;

    private $channelService;

    private $accountService;

    public function __construct(
        EntityManager $em,
        CustomerService $customer,
        Service $service,
        FeedbackRatingService $feedbackRatingService,
        $eventDispatcher,
        Channel $channelService,
        Account $accountService
    ) {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->service = $service;
        $this->feedbackRatingService = $feedbackRatingService;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelService = $channelService;
        $this->accountService = $accountService;
    }

    public function createNewFeedback($channelId)
    {
        $channel = $this->channelService->findById($channelId);
        $accountId = $channel->getAccountId();

        $feedback = $this->getNewFeedbackEntity($accountId, $channel->getId());

        $feedback->setService($this->getServiceEntity($channel->getId()));
        $feedback->setCustomer($this->getCustomerEntity($channel->getId()));

        $feedback = $this->addFeedbackCriterias($channel->getRatingCriteria(), $feedback);

        return $feedback;
    }

    public function store($feedback)
    {
        try {
            $this->saveEntity($feedback);
        } catch (DBALException $ex) {
            throw new FeedbackException('Email already exists');
        } catch (\Exception $ex) {
            throw new FeedbackException('Cannot create feedback');
        }
    }

    public function processFeedback(FeedbackEntity $feedback)
    {
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);

        $this->preserveRatingCriteriaOrder($feedback->getFeedbackRating());
        $this->store($feedback);

        $this->dispatchCreateFeedbackEvent($feedback);
    }

    public function remove($entity)
    {
        $this->removeEntity($entity);
    }

    public function getFeedbackType($channelId)
    {
        return new FeedbackType($this->service->getServiceFormType($channelId));
    }

    public function batchActivate($activateList, $inFeedbackList)
    {
        foreach ($inFeedbackList as $feedbackSummary) {
            $feedback = $feedbackSummary->getFeedback();
            $feedback->setIsEnabled(false);

            if (array_key_exists($feedback->getId(), $activateList)) {
                $feedback->setIsEnabled(true);
            }
            $this->entityManager->persist($feedback);
        }
        $this->entityManager->flush();
    }

    public function activateFeedback($feedbackId)
    {
        $feedback = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')->find($feedbackId);
        $feedback->setIsEnabled(true);
        $this->saveEntity($feedback);
    }

    public function setCustomerEntity($customerEntity)
    {
        $this->customerEntity = $customerEntity;
    }

    public function setServiceEntity($serviceEntity)
    {
        $this->serviceEntity = $serviceEntity;
    }

    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getNewFeedbackEntity($accountId, $channelId)
    {
        $feedback = new FeedbackEntity();
        $feedback->setAccountId($accountId);
        $feedback->setChannelId($channelId);
        return $feedback;
    }


    private function dispatchCreateFeedbackEvent(FeedbackEntity $feedback)
    {
        $customer = $feedback->getCustomer();
        $account = $this->accountService->findByAccountId($feedback->getAccountId());

        $event = new FeedbackNotificationEvent(
            $feedback,
            $account->getEmail(),
            $customer
        );
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_COMPLETE, $event);
    }

    private function getCustomerEntity($channelId)
    {
        if (!$this->customerEntity) {
            $this->customerEntity = $this->customerService->createNewCustomer($channelId);
        }
        return $this->customerEntity;
    }

    private function getServiceEntity($channelId)
    {
        if (!$this->serviceEntity) {
            $this->serviceEntity = $this->service->createNewService($channelId, $this->getCustomerEntity($channelId));
        }
        return $this->serviceEntity;
    }

    private function addFeedbackCriterias($ratingCriterias, $feedback)
    {
        foreach ($ratingCriterias as $criteria) {
            $feedbackRating = $this->feedbackRatingService->createNewFeedbackRating($criteria, $feedback);
            $feedback->addFeedbackRating($feedbackRating);
        }
        return $feedback;
    }

    private function preserveRatingCriteriaOrder($feedbackRatings)
    {
        foreach ($feedbackRatings as $rating) {
            $channelRatingCriteria = $this->entityManager
                ->getReference('MFBChannelBundle:ChannelRatingCriteria', $rating->getRatingCriteriaId());
            $rating->setRatingCriteria($channelRatingCriteria);
        }
        return $feedbackRatings;
    }
}