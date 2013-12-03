<?php

namespace MFB\AccountProfileBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MFB\FeedbackBundle\Specification\PreBuiltSpecification;

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
            return $this->render('MFBAccountProfileBundle:Default:no_feedbacks.html.twig');
        }

        $preBuiltSpec = new PreBuiltSpecification($account, $accountChannel);
        $feedbackSpecification = $preBuiltSpec->getFeedbackSpecification();
        $feedbackRatingSpecification = $preBuiltSpec->getFeedbackWithRatingSpecification();

        $feedbackRepo = $em->getRepository('MFBFeedbackBundle:Feedback');
        $feedbackCount = $feedbackRepo->getFeedbackCount($feedbackRatingSpecification);
        $feedbackRatingAverage = round($feedbackRepo->getRatingsAverage($feedbackRatingSpecification), 1);

        $feedbackList = $feedbackRepo->findSortedFeedbacks($feedbackSpecification, 'DESC', 100);

        return $this->render(
            'MFBAccountProfileBundle:Default:index.html.twig',
            array(
                'account_channel_name' => $accountChannel->getName(),
                'account_id' => $account->getId(),
                'feedbackList'=>$feedbackList,
                'ratingCount' => $feedbackCount,
                'ratingAverage' => $feedbackRatingAverage
            )
        );
    }
}
