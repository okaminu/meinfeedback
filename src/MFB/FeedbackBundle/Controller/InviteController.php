<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Manager\Feedback as FeedbackEntityManager;
use MFB\Template\Manager\TemplateManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        try {
            $feedbackEntityManager = new FeedbackEntityManager(
                $invite->getAccountId(),
                $accountChannel->getId(),
                $customer,
                $request->get('feedback'),
                $request->get('rating'),
                new FeedbackEntity()
            );

            $feedbackId = $feedbackEntityManager->saveFeedback(
                $em,
                $accountChannel->getRatingsEnabled()
            );

        } catch (FeedbackException $ex) {
            return $this->showFeedbackForm(
                $request->get('token'),
                $accountChannel,
                $request->get('feedback'),
                $ex->getMessage()
            );
        }

        $dispatcher->dispatch(FeedbackEvents::INVITE_COMPLETE);
        $this->get('mfb_email.sender')->sendFeedbackNotification(
            $account,
            $customer,
            $request->get('feedback'),
            $request->get('rating'),
            $this->get('router')->generate(
                'mfb_feedback_enable',
                array('feedbackId' => $feedbackId),
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );

        $em->remove($invite);
        $em->flush();

        $return_url = $this->getReturnUrl($accountChannel);

        return $this->render(
            'MFBFeedbackBundle:Invite:thank_you.html.twig',
            array(
                'thankyou_text' => $this->getThankYouText($em, $customer),
                'homepage' => $return_url
            )
        );

    }


    private function showFeedbackForm($token, $accountChannel, $feedback = '', $errorMessage = false)
    {
        return $this->render(
            'MFBFeedbackBundle:Invite:index.html.twig',
            array(
                'token' => $token,
                'accountChannel' => $accountChannel,
                'errorMessage' => $errorMessage,
                'feedback' => $feedback,
            )
        );
    }

    /**
     * @param $em
     * @param $customer
     * @return mixed
     */
    protected function getThankYouText($em, $customer)
    {
        $templateManager = new TemplateManager();
        $templateText = $templateManager->getThankYouText(
            $em,
            $customer->getAccountId(),
            $customer,
            $this->get('translator')
        );
        return $templateText;
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
}
