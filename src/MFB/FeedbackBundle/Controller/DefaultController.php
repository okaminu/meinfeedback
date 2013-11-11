<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;

class DefaultController extends Controller
{
    public function indexAction($accountId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exits');
        }

        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$account->getId())
        );
        if (!$accountChannel) {
            throw $this->createNotFoundException('Account does not have any channels');
        }


        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountId' => $account->getId(),
                'accountChannelId' => $accountChannel->getId(),
                'account_channel_name' => $accountChannel->getName(),
                'ratingEnabled' => $accountChannel->getRatingsEnabled(),
                'errorMessage' => false,
                'feedback' => ''
            )
        );
    }
}
