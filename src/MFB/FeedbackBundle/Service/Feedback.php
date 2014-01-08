<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\Service\FeedbackRating as FeedbackRatingService;
use MFB\FeedbackBundle\Entity\FeedbackSummary;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\ServiceBundle\Entity\ServiceGroup;
use MFB\ServiceBundle\Entity\ServiceProvider;
use MFB\ServiceBundle\Service\Service;
use MFB\CustomerBundle\Service\Customer as CustomerService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Feedback
{
    private $entityManager;

    private $customerService;

    private $service;

    private $eventDispatcher;

    private $feedbackOrder;
    
    private $serviceEntity = null;
    
    private $customerEntity = null;
    
    private $feedbackRatingService;

    private $accountId;

    public function __construct(
        EntityManager $em,
        CustomerService $customer,
        Service $service,
        FeedbackRatingService $feedbackRatingService,
        EventDispatcher $ed,
        $feedbackOrder
    ) {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->service = $service;
        $this->feedbackRatingService = $feedbackRatingService;
        $this->eventDispatcher = $ed;
        $this->feedbackOrder = $feedbackOrder;
    }

    public function createNewFeedback($accountId)
    {
        $this->setAccountId($accountId);
        $accountChannel = $this->getAccountChannel($accountId);
        $feedback = $this->getNewFeedbackEntity($accountId, $accountChannel->getId());

        $feedback->setService($this->getServiceEntity());
        $feedback->setCustomer($this->getCustomerEntity());

        $feedback = $this->addFeedbackCriterias($accountChannel->getRatingCriteria(), $feedback);

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

    public function getFeedbackType($accountId)
    {
        return new FeedbackType($this->service->getServiceType($accountId));
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
        $feedbackList = $this->getFeedbackList($accountId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getActiveFeedbackSummaryList($accountId)
    {
        $feedbackList = $this->getActiveFeedbackList($accountId);
        return $this->createFeedbackSummaryList($feedbackList);
    }

    public function getFeedbackList($accountId)
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array('accountId' => $accountId),
                $this->feedbackOrder
            );

        return $feedbackList;
    }

    public function getActiveFeedbackList($accountId)
    {
        $feedbackList = $this->entityManager->getRepository('MFBFeedbackBundle:Feedback')
            ->findBy(
                array('accountId' => $accountId, 'isEnabled' =>  1),
                $this->feedbackOrder
            );

        return $feedbackList;
    }


    public function batchActivate($activateList, $inFeedbackList)
    {
        foreach ($inFeedbackList as $feedback) {
            $feedback->setIsEnabled(false);

            if (array_key_exists($feedback->getId(), $activateList)) {
                $feedback->setIsEnabled(true);
            }
            $this->entityManager->persist($feedback);
        }
        $this->entityManager->flush();
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param null $customerEntity
     */
    public function setCustomerEntity($customerEntity)
    {
        $this->customerEntity = $customerEntity;
    }

    /**
     * @param null $serviceEntity
     */
    public function setServiceEntity($serviceEntity)
    {
        $this->serviceEntity = $serviceEntity;
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
            null
        );
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_COMPLETE, $event);
    }

    /**
     * @param $feedbackList
     * @return array
     */
    private function createFeedbackSummaryList($feedbackList)
    {
        $feedbackSummaryList = array();
        foreach ($feedbackList as $feedback) {
            $feedbackSummaryList[] = $this->createFeedbackSummaryItem($feedback);
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


    /**
     * @return null
     */
    private function getCustomerEntity()
    {
        if (!$this->customerEntity) {
            $this->customerEntity = $this->customerService->createNewCustomer($this->getAccountId());
        }
        return $this->customerEntity;
    }

    /**
     * @return null
     */
    private function getServiceEntity()
    {
        if (!$this->serviceEntity) {
            $this->serviceEntity = $this->service->createNewService($this->getAccountId(), $this->getCustomerEntity());
        }
        return $this->serviceEntity;
    }

    /**
     * @param $ratingCriterias
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return Feedback
     */
    private function addFeedbackCriterias($ratingCriterias, $feedback)
    {
        foreach ($ratingCriterias as $criteria) {
            $feedbackRating = $this->feedbackRatingService->createNewFeedbackRating($criteria, $feedback);
            $feedback->addFeedbackRating($feedbackRating);
        }
        return $feedback;
    }

    /**
     * @param $feedbackRatings
     */
    private function preserveRatingCriteriaOrder($feedbackRatings)
    {
        /**
         * @var $rating \MFB\FeedbackBundle\Entity\FeedbackRating
         */
        foreach ($feedbackRatings as $rating) {
            $channelRatingCriteria = $this->entityManager
                ->getReference('MFBChannelBundle:ChannelRatingCriteria', $rating->getRatingCriteriaId());
            $rating->setRatingCriteria($channelRatingCriteria);
        }
        return $feedbackRatings;
    }

    /**
     * @param FeedbackSummary $singleSummary
     * @param ServiceGroup $serviceGroup
     * @param ServiceProvider $serviceProvider
     * @return FeedbackSummary
     */
    private function addServiceSummary(
        FeedbackSummary $singleSummary,
        ServiceGroup $serviceGroup,
        ServiceProvider $serviceProvider
    ) {
        $singleSummary->setServiceTypeName($serviceGroup->getName());
        $serviceProviderInfo = $serviceProvider->getFirstname() . ' ' . $serviceProvider->getLastname();
        $singleSummary->setServiceProviderInfo($serviceProviderInfo);
        return $singleSummary;
    }

    /**
     * @param \MFB\FeedbackBundle\Entity\Feedback $feedback
     * @return FeedbackSummary
     */
    private function createFeedbackSummaryItem($feedback)
    {
        $singleSummary = new FeedbackSummary();
        $service = $feedback->getService();
        $singleSummary = $this->addServiceSummary(
            $singleSummary,
            $service->getServiceGroup(),
            $service->getServiceProvider()
        );
        $singleSummary->setRating($this->calcFeedbackRatingAverage($feedback));
        $singleSummary->setFeedback($feedback);
        return $singleSummary;
    }

}