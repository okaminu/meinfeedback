<?php
namespace MFB\CustomerBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use MFB\AccountBundle\AccountException;
use MFB\CustomerBundle\Entity\Customer as CustomerEntity;
use MFB\CustomerBundle\CustomerEvents;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Customer
{
    private $entityManager;

    private $eventDispacher;

    public function __construct(EntityManager $em, EventDispatcher $ed)
    {
        $this->entityManager = $em;
        $this->eventDispacher = $ed;
    }

    public function createNewCustomer($accountId)
    {
        $customer = $this->getNewCustomerEntity($accountId);
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

    private function getNewCustomerEntity($accountId)
    {
        $accountChannel = $this->getAccountChannel($accountId);

        if ($accountChannel == null) {
            throw new AccountException('No account data found. Please fill Account setup form.');
        }
        $customer = new CustomerEntity();
        $customer->setAccountId($accountId);
        $customer->setChannelId($accountChannel->getId());
        return $customer;
    }


}