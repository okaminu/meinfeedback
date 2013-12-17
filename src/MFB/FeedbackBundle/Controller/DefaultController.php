<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\ServiceBundle\Form\ServiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    public function showCreateFeedbackFormAction($accountId)
    {
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($accountId);
        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId);
        $form = $this->getFeedbackForm($feedback, $accountId, $accountChannel->getId());
        return $this->showFeedbackFrom($accountChannel, $form);
    }

    public function saveFeedbackAction(Request $request)
    {
        $accountId = $request->get('accountId');
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($accountId);
        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId);
        $form = $this->getFeedbackForm($feedback, $accountId, $accountChannel->getId());
        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }

            $this->get('mfb_feedback.service')->store($feedback);
            return $this->showThankyouForm($accountChannel, $feedback);
        } catch (FeedbackException $ax) {
            $form->addError(new FormError($ax->getMessage()));
        } catch (\Exception $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showFeedbackFrom($accountChannel, $form);
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

    protected function getFeedbackEnableLink($id)
    {
        return $url = $this->generateUrl(
            'mfb_feedback_enable',
            array('feedback_id' => $id)
        );
    }

    /**
     * @param $feedback
     * @param $accountId
     * @param $accountChannelId
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm(Feedback $feedback, $accountId, $accountChannelId)
    {
        $serviceType = $this->get('mfb_service.service')->getServiceType($accountId);

        $form = $this->createForm(new FeedbackType($serviceType), $feedback, array(
            'action' => $this->generateUrl('mfb_feedback_save', array(
                        'accountId' => $accountId,
                        'accountChannelId' => $accountChannelId
                    )),
                'method' => 'POST'
            ));
        return $form;
    }

    /**
     * @param $accountChannel
     * @param $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function showFeedbackFrom($accountChannel, $form)
    {
        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountChannel' => $accountChannel,
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param $accountChannel
     * @param $feedback
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function showThankyouForm($accountChannel, $feedback)
    {
        $return_url = $this->getReturnUrl($accountChannel);

        return $this->render(
            'MFBFeedbackBundle:Default:thank_you.html.twig',
            array(
                'thankyou_text' => $this->get('mfb_email.template')->getText($feedback->getCustomer(), 'ThankYou'),
                'homepage' => $return_url
            )
        );
    }
}
