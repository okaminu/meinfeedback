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

        return $this->showFeedbackForm($token, $accountChannel);
    }

    public function saveAction(Request $request)
    {
        $rating = null;
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

        $requestRating = (int)$request->get('rating');

        if (($requestRating > 0) && ($requestRating <= 5)) {
            $rating = $requestRating;
        }

        if (($accountChannel->getRatingsEnabled() == '1') && (is_null($rating))) {
            return $this->showFeedbackForm(
                $request->get('token'),
                $accountChannel,
                $request->get('feedback'),
                'Please select star rating'
            );
        }

        $feedback->setRating($rating);
        $em->persist($feedback);
        $em->remove($invite);
        $em->flush();

        return $this->render('MFBFeedbackBundle:Invite:thank_you.html.twig');

    }

    private function showFeedbackForm($token, $accountChannel, $feedback = '', $starErrorMessage = false)
    {
        return $this->render(
            'MFBFeedbackBundle:Invite:index.html.twig',
            array(
                'token' => $token,
                'accountChannel' => $accountChannel,
                'starErrorMessage' => $starErrorMessage,
                'feedback' => $feedback,
            )
        );
    }
}
