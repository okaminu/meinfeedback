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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class InviteController extends Controller
{

    /**
     * @Route("/createFeedback/invite/{token}", name="mfb_feedback_create_with_invite")
     */
    public function showCreateFeedbackFormAction(Request $request)
    {

        $token = $request->get('token');
        $invite = $this->getInvitation($token);
        if (!$invite) {
            return $this->render('MFBFeedbackBundle:Invite:no_invite.html.twig');
        }
        $accountId = $invite->getAccountId();
        $accountChannel = $this->getAccountChannel($accountId);

        $feedbackService = $this->get('mfb_feedback.service');
        $feedbackService->setServiceEntity($invite->getService());

        $form = $this->getFeedbackForm($token, $feedbackService->createNewFeedback($accountId));
        return $this->showFeedbackForm($accountChannel, $form);
    }

    /**
     * @Route("/saveFeedback/invite/{token}", name="mfb_feedback_save_with_invite")
     * @Method({"POST"})
     */

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

        $feedbackService = $this->get('mfb_feedback.service');
        $feedbackService->setServiceEntity($service);
        $feedbackService->setCustomerEntity($service->getCustomer());
        $feedback= $feedbackService->createNewFeedback($accountId);

        $form = $this->getFeedbackForm($token, $feedback);
        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_feedback_invite.service')->processInviteFeedback($invite, $feedback);

            return $this->showThankyouForm(
                $accountChannel,
                $service->getCustomer(),
                $this->container->getParameter('mfb_feedback.redirectTimeout')
            );

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

        $this->addCriteriaLabels($form);

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
        $accountChannel = $this->get("mfb_account_channel.service")->findByAccountId($accountId);
        return $accountChannel;
    }

    /**
     * @param $accountChannel
     * @param $customer
     * @param $redirectTimeout
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function showThankyouForm($accountChannel, $customer, $redirectTimeout)
    {
        $return_url = $this->getReturnUrl($accountChannel);

        return $this->render(
            'MFBFeedbackBundle::thank_you.html.twig',
            array(
                'thankyou_text' => $this->get('mfb_email.template')->getText($customer, 'ThankYou'),
                'homepage' => $return_url,
                'redirectTimeout' => $redirectTimeout
            )
        );
    }

    /**
     * @param $form
     */
    private function addCriteriaLabels($form)
    {
        $feedbackRatingForms = $form->get('feedbackRating');
        foreach ($feedbackRatingForms as $ratingForm) {
            $channelCriteria = $ratingForm->getData()->getRatingCriteria();
            $ratingForm->remove('rating');
            $ratingForm->add('rating', 'hidden', array('label' => $channelCriteria->getName()));
        }
    }

}
