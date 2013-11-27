<?php

namespace MFB\AccountBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountManager extends EntityRepository
{


    public function findAccountByAccountId($accountId)
    {
        /** @var Account $account */
        $account = $this->getEntityManager()->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw new NotFoundHttpException('Account does not exits');
        }
        return $account;
    }

    public function findAccountByUsernameOrEmail($username)
    {
        /** @var Account $account */
        $account = $this->getEntityManager()->getRepository('MFBAccountBundle:Account')->findOneBy(
            array('email' => $username)
        );
        return $account;
    }

    public function updateAccount(Account $account)
    {
        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush();
    }
}
