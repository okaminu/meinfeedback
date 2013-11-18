<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use MFB\FeedbackBundle\FeedbackException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\FeedbackBundle\Manager\Feedback as FeedbackEntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\Template\Manager\TemplateManager;

use MFB\Template\Placeholder\PlaceholderContainer;

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

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $invite->getAccountId());
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

            $feedbackEntityManager->saveFeedback(
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

        $this->get('mfb_email.sender')->sendFeedbackNotification(
            $account,
            $customer,
            $request->get('feedback'),
            $request->get('rating')
        );

        $templateText = $this->getThankYouText($em, $invite, $customer);

        $em->remove($invite);
        $em->flush();

        return $this->render(
            'MFBFeedbackBundle:Invite:thank_you.html.twig',
            array(
                'thankyou_text' => $templateText,
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
     * @param $invite
     * @param $customer
     * @return mixed
     */
    protected function getThankYouText($em, $invite, $customer)
    {
        $templateManager = new TemplateManager();
        $templateText = $templateManager->getThankYouText(
            $em,
            $invite->getAccountId(),
            $customer,
            $this->get('translator')
        );
        return $templateText;
    }
}
