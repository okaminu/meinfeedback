<?php

namespace MFB\AccountProfileBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/feedbacks/{accountId}/{feedbackPage}", name="mfb_account_profile_homepage",
     * requirements={"feedbackPage" = "\d+"}, defaults={"feedbackPage" = "1"})
     * @Template
     */
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
        $documents = $this->get('mfb_document.service')->findByCategory($accountChannel->getId(), 'logo');

        $logoPath = '';
        if ($document = end($documents)) {
            $logoPath = $document->getWebPath();
        }

        $channelFeedbacks = $this->get('mfb_feedback_display.service')->getChannelFeedbacks($accountChannel->getId());
        $channelAddress = "{$accountChannel->getCity()}  {$accountChannel->getPlace()} {$accountChannel->getStreet()}";
        return
            array(
                'account_channel_name' => $accountChannel->getName(),
                'account_id' => $account->getId(),
                'feedbackSummaryPage'=> $channelFeedbacks->getActiveFeedbackSummary($feedbackPage),
                'ratingCount' => $channelFeedbacks->getChannelFeedbackCount(),
                'channelRatingSummaryList' => $channelFeedbacks->getChannelRatingSummary(),
                'channelAddress' => $channelAddress,
                'channel' => $accountChannel,
                'logoUrl' => $logoPath,
                'baseUrl' => $this->generateUrl('mfb_account_profile_homepage', array('accountId' => $accountId))
        );
    }
}
