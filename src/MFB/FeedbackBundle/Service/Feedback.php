<?php
namespace MFB\FeedbackBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Service\Service;
use MFB\CustomerBundle\Service\Customer as CustomerService;

class Feedback
{
    private $entityManager;

    private $customerService;

    private $service;


    public function __construct(EntityManager $em, CustomerService $customer, Service $service)
    {
        $this->entityManager = $em;
        $this->customerService = $customer;
        $this->service = $service;
    }

    public function createNewFeedback($accountId)
    {
        $accountChannelId = $this->getAccountChannel($accountId)->getId();
        $feedback = $this->getNewFeedbackEntity($accountId, $accountChannelId);
        $customer = $this->customerService->createNewCustomer($accountId);
        $service = $this->service->createNewService($accountId);

        $service->setCustomer($customer);
        $feedback->setService($service);

        return $feedback;
    }

    public function store($feedback)
    {
        try {
            $this->saveEntity($feedback);
        } catch (\Exception $ex) {
            throw new FeedbackException('Cannot create feedback');
        }
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

    private function getNewFeedbackEntity($accountId, $channelId)
    {
        $feedback = new FeedbackEntity();
        $feedback->setAccountId($accountId);
        $feedback->setChannelId($channelId);
        return $feedback;
    }

}