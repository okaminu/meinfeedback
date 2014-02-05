<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\FeedbackException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/createFeedback/{accountId}", name="mfb_feedback_create")
     */
    public function showCreateFeedbackFormAction($accountId)
    {
        $channel = $this->get("mfb_account_channel.service")->findByAccountId($accountId);
        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($channel->getId());
        $form = $this->getFeedbackForm($feedback, $channel->getAccountId(), $channel->getId());
        return $this->showFeedbackFrom($channel, $form);
    }

    /**
     * @Route("/saveFeedback/{accountId}/{accountChannelId}", name="mfb_feedback_save",
     * requirements={"accountId" = "\d+", "accountChannelId" = "\d+"})
     * @Method({"POST"})
     */
    public function saveFeedbackAction(Request $request)
    {
        $accountId = $request->get('accountId');
        $channel = $this->get("mfb_account_channel.service")->findByAccountId($accountId);
        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($channel->getId());
        $form = $this->getFeedbackForm($feedback, $channel->getAccountId(), $channel->getId());
        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_feedback.service')->processFeedback($feedback);
            return $this->showThankyouForm(
                $channel,
                $feedback,
                $this->container->getParameter('mfb_feedback.redirectTimeout')
            );
        } catch (FeedbackException $ax) {
            $form->addError(new FormError($ax->getMessage()));
        } catch (\Exception $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showFeedbackFrom($channel, $form);
    }


    /**
     * @param $accountChannel
     * @return string
     */
    protected function getReturnUrl($accountChannel)
    {
        $return_url = $this->generateUrl(
            'mfb_account_profile_homepage',
            array('accountId' => $accountChannel->getAccountId(), 'feedbackPage' => 1),
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

    private function getFeedbackForm(Feedback $feedback, $accountId, $channelId)
    {
        $feedbackType = $this->get('mfb_feedback.service')->getFeedbackType($channelId);
        $form = $this->createForm($feedbackType, $feedback, array(
            'action' => $this->generateUrl('mfb_feedback_save', array(
                        'accountId' => $accountId,
                        'accountChannelId' => $channelId
                    )),
                'method' => 'POST'
            ));

        $this->addCriteriaLabels($form);

        return $form;
    }

    private function showFeedbackFrom($accountChannel, $form)
    {
        return $this->render(
            'MFBFeedbackBundle:Default:showCreateFeedbackForm.html.twig',
            array(
                'accountChannel' => $accountChannel,
                'form' => $form->createView()
            )
        );
    }

    private function showThankyouForm($accountChannel, $feedback, $redirectTimeout)
    {
        $return_url = $this->getReturnUrl($accountChannel);
        return $this->render(
            'MFBFeedbackBundle::thank_you.html.twig',
            array(
                'thankyou_text' => $this->get('mfb_email.template')->getText($feedback->getCustomer(), 'ThankYou'),
                'homepage' => $return_url,
                'redirectTimeout' => $redirectTimeout
            )
        );
    }

    private function addCriteriaLabels($form)
    {
        $feedbackRatingForms = $form->get('feedbackRating');
        foreach ($feedbackRatingForms as $ratingForm) {
            $channelCriteria = $ratingForm->getData()->getRatingCriteria();
            $ratingForm->remove('rating');
            $ratingForm->add('rating', 'hidden', array('label' => $channelCriteria->getRatingCriteria()->getName()));
        }
    }
}
