<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InviteController extends Controller
{
    public function indexAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $invite = $em->getRepository('MFBFeedbackBundle:FeedbackInvite')->findOneBy(
            array('token'=>$token)
        );
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }
        $accountChannel = $em->find('MFBChannelBundle:AccountChannel', $invite->getChannelId());

        return $this->render(
            'MFBFeedbackBundle:Invite:index.html.twig',
            array(
                'token' => $token,
                'account_channel_name' => $accountChannel->getName(),
                'ratingEnabled' => $accountChannel->getRatingsEnabled(),
                'errorMessage' => false,
                'feedback' => ''
            )
        );
    }

    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var FeedbackInvite $invite */
        $invite = $em->getRepository('MFBFeedbackBundle:FeedbackInvite')->findOneBy(
            array('token'=>$request->get('token'))
        );
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }

        $accountChannel = $em->find('MFBChannelBundle:AccountChannel', $invite->getChannelId());

        $feedback = new Feedback();
        $feedback->setAccountId($invite->getAccountId());
        $feedback->setChannelId($invite->getChannelId());
        $feedback->setCustomerId($invite->getCustomerId());
        $feedback->setContent($request->get('feedback'));

        $rating = null;

        $requestRating = (int)$request->get('rating');

        if (($requestRating > 0) && ($requestRating <= 5)) {
            $rating = $requestRating;
        }

        if (($accountChannel->getRatingsEnabled() == '1') && (is_null($rating))) {
            return $this->render(
                'MFBFeedbackBundle:Invite:index.html.twig',
                array(
                    'token' => $request->get('token'),
                    'account_channel_name' => $accountChannel->getName(),
                    'ratingEnabled' => $accountChannel->getRatingsEnabled(),
                    'errorMessage' => 'Please select star rating',
                    'feedback' => $request->get('feedback')
                )
            );
        }

        $feedback->setRating($rating);
        $em->persist($feedback);
        $em->remove($invite);
        $em->flush();

        return $this->render('MFBFeedbackBundle:Invite:thank_you.html.twig');

    }
}
