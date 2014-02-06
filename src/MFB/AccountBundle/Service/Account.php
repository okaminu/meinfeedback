<?php

namespace MFB\AccountBundle\Service;

use MFB\AccountBundle\AccountException;
use MFB\AccountBundle\Entity\Account as AccountEntity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Account
{
    private $entityManager;

    public function __construct($em)
    {
        $this->entityManager = $em;
    }

    public function createNew()
    {
        $account = new AccountEntity();
        $account->setIsEnabled(false);
        $account->setIsLocked(false);
        $account->setHash($this->generateRandomHash());
        return $account;
    }

    public function store($account)
    {
        try {
            $this->saveEntity($account);
        } catch (\Exception $ex) {
            throw new AccountException('Cannot create account');
        }
    }

    public function enableAccountByHash($hash)
    {
        $account = $this->findByHash($hash);

        $account->setIsEnabled(true);
        $this->store($account);
    }

    public function enableAccount($accountId)
    {
        $account = $this->findByAccountId($accountId);

        $account->setIsEnabled(true);
        $this->store($account);
    }

    public function disableAccount($accountId)
    {
        $account = $this->findByAccountId($accountId);

        $account->setIsEnabled(false);
        $this->store($account);
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findByAccountId($accountId)
    {
        $account = $this->entityManager->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw new NotFoundHttpException('Account does not exits');
        }
        return $account;
    }

    public function findByEmail($username)
    {
        $account = $this->entityManager->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('email' => $username)
        );
        return $account;
    }

    public function findByHash($hash)
    {
        $account = $this->entityManager->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('hash' => $hash)
        );
        return $account;
    }

    private function generateRandomHash()
    {
        return sha1(uniqid(mt_rand(), true));
    }
}
