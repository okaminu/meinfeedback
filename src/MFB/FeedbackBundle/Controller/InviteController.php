<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\FeedbackBundle\Form\FeedbackInviteType;

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
        $customer = $em->find('MFBCustomerBundle:Customer', $invite->getCustomerId());

        $feedback = new Feedback();
        $feedback->setChannelId($accountChannel->getId());
        $feedback->setAccountId($accountChannel->getAccountId());
        $feedback->setCustomer($customer);

        $form = $this->getFeedbackForm($token, $feedback);
        return $this->render(
            'MFBFeedbackBundle:Invite:index.html.twig',
            array(
                'accountChannel' => $accountChannel,
                'form' => $form->createView()
            )
        );
    }

    public function saveAction(Request $request)
    {
        $rating = null;
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(FeedbackEvents::INVITE_INITIALIZE);
        $em = $this->getDoctrine()->getManager();

        /** @var FeedbackInvite $invite */
        $invite = $em->getRepository('MFBFeedbackBundle:FeedbackInvite')->findOneBy(
            array('token'=>$request->get('token'))
        );
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $invite->getAccountId());
        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->find('MFBChannelBundle:AccountChannel', $invite->getChannelId());
        $customer = $em->find('MFBCustomerBundle:Customer', $invite->getCustomerId());

        $feedback = new Feedback();
        $feedback->setChannelId($accountChannel->getId());
        $feedback->setAccountId($accountChannel->getAccountId());
        $feedback->setCustomer($customer);

        $form = $this->getFeedbackForm($request->get('token'), $feedback);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($feedback);
            $em->flush();

            $event = new CustomerAccountEvent($feedback->getId(), $account, $customer, $request, $invite);
            $dispatcher->dispatch(FeedbackEvents::INVITE_COMPLETE, $event);

            $em->remove($invite);
            $em->flush();

            $return_url = $this->getReturnUrl($accountChannel);

            return $this->render(
                'MFBFeedbackBundle:Invite:thank_you.html.twig',
                array(
                    'thankyou_text' => $this->get('mfb_email.template')->getText($customer, 'ThankYou'),
                    'homepage' => $return_url
                )
            );
        }
        return $this->render(
            'MFBFeedbackBundle:Invite:index.html.twig',
            array(
                'accountChannel' => $accountChannel,
                'form' => $form->createView()
            )
        );

    }

    /**
     * @param $accountChannel
     * @return string
     */
    protected function getReturnUrl($accountChannel)
    {
        $return_url = $this->generateUrl(
            'mfb_account_profile_homepage',
            array('accountId' => $accountChannel->getAccountId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        if ($accountChannel->getHomepageUrl()) {
            $return_url = $accountChannel->getHomepageUrl();
        }
        return $return_url;
    }

    /**
     * @param $token
     * @param $feedback
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm($token, Feedback $feedback)
    {
        $form = $this->createForm(
            new FeedbackInviteType(),
            $feedback,
            array(
                'action' => $this->generateUrl(
                    'mfb_feedback_save_with_invite',
                    array(
                        'accountId' => $feedback->getAccountId(),
                        'token' => $token
                        )
                ),
                'method' => 'POST',
            )
        );
        return $form;
    }
}
