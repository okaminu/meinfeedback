<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\Entity\FeedbackSummary;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Service\Service;
use MFB\CustomerBundle\Service\Customer as CustomerService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Feedback
{
    private $entityManager;

    private $customerService;

    private $service;

    private $eventDispatcher;
    
    public function __construct(EntityManager $em, CustomerService $customer, Service $service, EventDispatcher $ed)
    {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->service = $service;
        $this->eventDispatcher = $ed;
    }

    public function createNewFeedback($accountId, $service = null, $customer = null)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $feedback = $this->getNewFeedbackEntity($accountId, $accountChannelId);
        if (!$customer) {
            $customer = $this->customerService->createNewCustomer($accountId);
        }
        if (!$service) {
            $service = $this->service->createNewService($accountId, $customer);
        }
        $feedback->setService($service);
        $feedback->setCustomer($customer);

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


    public function processFeedback($feedback)
    {
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);
        $this->store($feedback);
        $this->dispatchCreateFeedbackEvent($feedback);
    }

    public function remove($entity)
    {
        $this->removeEntity($entity);
    }

    public function getFeedbackCount($accountId)
    {
        $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getAccountFeedbackCount($accountId);
        return $feedbackCount;
    }

    public function getFeedbackRatingAverage($accountId)
    {
        $ratingAverage = $feedbackCount = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->getFeedbackRatingAverage($accountId);

        return $this->roundHalfUp($ratingAverage);
    }

    public function getFeedbackSummaryList($accountId)
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array('accountId' => $accountId, 'isEnabled' =>  1),
                array('createdAt' => 'DESC')
            );

        return $this->createFeedbackSummary($feedbackList);
    }

    private function roundHalfUp($number)
    {
        return round($number, 0, PHP_ROUND_HALF_UP);
    }


    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
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

    private function getAccount($accountId)
    {
        $account = $this->entityManager->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('id' => $accountId)
        );
        return $account;
    }

    private function getNewFeedbackEntity($accountId, $channelId)
    {
        $feedback = new FeedbackEntity();
        $feedback->setAccountId($accountId);
        $feedback->setChannelId($channelId);
        return $feedback;
    }


    /**
     * @param $feedback
     */
    private function dispatchCreateFeedbackEvent(FeedbackEntity $feedback)
    {
        $customer = $feedback->getCustomer();
        $account = $this->getAccount($feedback->getAccountId());

        $event = new CustomerAccountEvent(
            $feedback->getId(),
            $account->getEmail(),
            $customer,
            $feedback->getContent(),
            $feedback->getRating()
        );
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_COMPLETE, $event);
    }

    /**
     * @param $feedbackList
     * @return array
     */
    private function createFeedbackSummary($feedbackList)
    {
        $feedbackSummaryList = array();
        foreach ($feedbackList as $feedback) {
            $singleSummary = new FeedbackSummary();
            $singleSummary->setFeedback($feedback);
            $singleSummary->setRating($this->calcFeedbackRatingAverage($feedback));
            $feedbackSummaryList[] = $singleSummary;
        }
        return $feedbackSummaryList;
    }

    /**
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return float
     */
    private function calcFeedbackRatingAverage(FeedbackEntity $feedback)
    {
        $ratingAverage = array();
        foreach ($feedback->getFeedbackRating() as $rating) {
            $ratingAverage[] = $rating->getRating();
        }
        $average = array_sum($ratingAverage) / count($ratingAverage);
        return $this->roundHalfUp($average);
    }
}