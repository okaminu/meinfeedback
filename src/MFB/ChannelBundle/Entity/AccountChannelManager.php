<?php


namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MFB\AccountBundle\Entity\Account;

class AccountChannelManager extends EntityRepository
{
    /**
     * Get Account Channel by Account
     *
     * @param $accountId
     * @return AccountChannel
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findAccountChannelByAccount($accountId)
    {
        $accountChannel = $this->getEntityManager()->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$accountId)
        );
        if (!$accountChannel) {
            throw new NotFoundHttpException('No feedback yet. Sorry.');
        }

        return $accountChannel;
    }

}
