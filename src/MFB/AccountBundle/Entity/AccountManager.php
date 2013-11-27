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

    public function hasNonExpiredRequest(Account $account, $time)
    {
        return $account->getPasswordRequestedAt() instanceof \DateTime &&
        $account->getPasswordRequestedAt()->getTimestamp() + $time > time();
    }

    public function getResetToken(Account $account)
    {
        return $account->getResetToken();
    }

    public function updateAccount(Account $account)
    {
        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush();
    }


//    public function updatePassword(Account $account)
//    {
//        if (0 !== strlen($password = $account->getPlainPassword())) {
//            /** @var EncoderFactoryInterface $encoder */
//            $encoder = $this->getEncoderFactory()->getEncoder($account);
//            $account->setSalt(base64_encode($this->getSecureRandom()->nextBytes(20)));
//            $account->setPassword($encoder->encodePassword($password, $account->getSalt()));
//        }
//    }




}
