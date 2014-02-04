<?php
namespace MFB\CustomerBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\AccountException;
use MFB\ChannelBundle\Service\Channel;
use MFB\CustomerBundle\Entity\Customer as CustomerEntity;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Customer
{
    private $entityManager;

    private $eventDispacher;
    
    private $channelService;

    public function __construct(EntityManager $em, $ed, Channel $channelService)
    {
        $this->entityManager = $em;
        $this->eventDispacher = $ed;
        $this->channelService = $channelService;
    }

    public function createNewCustomer($channelId)
    {
        $customer = $this->getNewCustomerEntity($channelId);
        return $customer;

    }

    public function store($customer)
    {
        try {
            $this->saveEntity($customer);
        } catch (DBALException $ex) {
            if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                throw new AccountException('Email already exists');
            } else {
                throw new AccountException($ex->getMessage());
            }
        }
    }

    public function findByChannelId($channelId)
    {
        return $this->entityManager->getRepository('MFBCustomerBundle:Customer')->findAll(
            array('channel' => $channelId)
        );
    }

    public function removeList($list)
    {
        try {
            foreach ($list as $single) {
                $this->entityManager->remove($single);
            }
            $this->entityManager->flush();
        } catch (DBALException $ex) {
            throw new  AccountException('Cannot remove customer');
        }
    }


    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getNewCustomerEntity($channelId)
    {
        $accountChannel = $this->channelService->findById($channelId);

        if ($accountChannel == null) {
            throw new AccountException('No account data found. Please fill Account setup form.');
        }
        $customer = new CustomerEntity();
        $customer->setAccountId($accountChannel->getAccountId());
        $customer->setChannelId($accountChannel->getId());
        return $customer;
    }

}