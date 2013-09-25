<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $invite = $em->getRepository('MFBFeedbackBundle:FeedbackInvite')->findOneBy(
            array('token'=>$token)
        );
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Default:no_invite.html.twig');
        }
        $accountChannel = $em->find('MFBChannelBundle:AccountChannel', $invite->getChannelId());
        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'token' => $token,
                'account_channel_name' => $accountChannel->getName()
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
            return $this->render('MFBFeedbackBundle:Default:no_invite.html.twig');
        }

        $feedback = new Feedback();
        $feedback->setAccountId($invite->getAccountId());
        $feedback->setChannelId($invite->getChannelId());
        $feedback->setCustomerId($invite->getCustomerId());
        $feedback->setContent($request->get('feedback'));

        $em->persist($feedback);
        $em->remove($invite);
        $em->flush();

        return $this->render('MFBFeedbackBundle:Default:thank_you.html.twig');

    }
}
