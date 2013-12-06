<?php

namespace MFB\AccountBundle\Service;

use MFB\AccountBundle\Entity\Account as AccountEntity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Account
{
    private $em;

    public function __construct($em)
    {
           $this->em = $em;
    }

    public function findByAccountId($accountId)
    {
        /** @var Account $account */
        $account = $this->em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw new NotFoundHttpException('Account does not exits');
        }
        return $account;
    }

    public function findByEmail($username)
    {
        /** @var Account $account */
        $account = $this->em->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('email' => $username)
        );
        return $account;
    }

    public function addAccount(AccountEntity $account)
    {
        $this->em->persist($account);
        $this->em->flush();
    }
}
