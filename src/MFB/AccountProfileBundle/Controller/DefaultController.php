<?php

namespace MFB\AccountProfileBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($accountId, $feedbackPage)
    {
        /** @var Account $account */
        $account = $this->get('mfb_account.service')->findByAccountId($accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exits');
        }

        /** @var AccountChannel $accountChannel */
        $accountChannel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);

        if (!$accountChannel) {
            return $this->render('MFBAccountProfileBundle:Default:no_feedbacks.html.twig');
        }

        $channelFeedbacks = $this->get('mfb_feedback_display.service')->getChannelFeedbacks($accountChannel->getId());
        $channelAddress = "{$accountChannel->getCity()}  {$accountChannel->getPlace()} {$accountChannel->getStreet()}";
        return $this->render(
            'MFBAccountProfileBundle:Default:index.html.twig',
            array(
                'account_channel_name' => $accountChannel->getName(),
                'account_id' => $account->getId(),
                'feedbackSummaryPage'=> $channelFeedbacks->getActiveFeedbackSummary($feedbackPage),
                'ratingCount' => $channelFeedbacks->getChannelFeedbackCount(),
                'channelRatingSummaryList' => $channelFeedbacks->getChannelRatingSummary(),
                'channelAddress' => $channelAddress,
                'channel' => $accountChannel
            )
        );
    }
}
