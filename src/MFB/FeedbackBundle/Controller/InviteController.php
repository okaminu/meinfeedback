<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\FeedbackException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Form\FeedbackInviteType;

class InviteController extends Controller
{
    public function showCreateFeedbackFormAction(Request $request)
    {

        $token = $request->get('token');
        $invite = $this->getInvitation($token);
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }

        $accountId = $invite->getAccountId();

        $accountChannel = $this->getAccountChannel($accountId);

        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId, $invite->getService());
        $form = $this->getFeedbackForm($token, $feedback);
        return $this->showFeedbackForm($accountChannel, $form);
    }

    public function saveFeedbackAction(Request $request)
    {
        $token = $request->get('token');
        $invite = $this->getInvitation($token);
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }
        $accountId = $invite->getAccountId();

        $service = $invite->getService();
        $accountChannel = $this->getAccountChannel($accountId);

        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId, $service, $service->getCustomer());

        $form = $this->getFeedbackForm($token, $feedback);
        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_feedback_invite.service')->processInviteFeedback($invite, $feedback);

            return $this->showThankyouForm($accountChannel, $service->getCustomer());

        } catch (FeedbackException $ax) {
            $form->addError(new FormError($ax->getMessage()));
        } catch (\Exception $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showFeedbackForm($accountChannel, $form);
    }

    private function showFeedbackForm($accountChannel, $form)
    {
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

    private function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }


    /**
     * @param $token
     * @return mixed
     */
    private function getInvitation($token)
    {
        $em = $this->getEntityManager();
        $invite = $em->getRepository('MFBFeedbackBundle:FeedbackInvite')->findOneBy(
            array('token' => $token)
        );
        return $invite;
    }

    /**
     * @param $accountId
     * @return AccountChannel
     */
    private function getAccountChannel($accountId)
    {
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($accountId);
        return $accountChannel;
    }

    /**
     * @param $accountChannel
     * @param $customer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function showThankyouForm($accountChannel, $customer)
    {
        $return_url = $this->getReturnUrl($accountChannel);

        return $this->render(
            'MFBFeedbackBundle:Invite:thank_you.html.twig',
            array(
                'thankyou_text' => $this->get('mfb_email.template')->getText($customer, 'ThankYou'),
                'homepage' => $return_url
            )
        );
    }
}
