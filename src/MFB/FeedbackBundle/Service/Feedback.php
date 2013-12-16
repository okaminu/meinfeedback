<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Service\Service;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
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

    public function createNewFeedback($accountId, $service = null)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $feedback = $this->getNewFeedbackEntity($accountId, $accountChannelId);
        $customer = $this->customerService->createNewCustomer($accountId);
        if (!$service) {
            $service = $this->service->createNewService($accountId, $customer);
        }

        $feedback->setService($service);
        $feedback->setCustomer($customer);

        return $feedback;
    }


    public function store($feedback)
    {
        $this->eventDispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);

        try {
            $this->saveEntity($feedback);
        } catch (DBALException $ex) {
            if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                throw new FeedbackException('Email already exists');
            } else {
                throw new FeedbackException('Cannot create feedback');
            }
        } catch (\Exception $ex) {
            throw new FeedbackException('Cannot create feedback');
        }
        $this->dispatchCreateFeedbackEvent($feedback);
    }


    public function remove($entity)
    {
        $this->removeEntity($entity);
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
}