<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\FeedbackBundle\Manager\Feedback as FeedbackEntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\Template\ThankYouTemplate;
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


        if ($request->get('feedback') == '') {
            return $this->showFeedbackForm(
                $request->get('token'),
                $accountChannel,
                $request->get('feedback'),
                'Please leave a feedback'
            );
        }

        $feedbackEntityManager = new FeedbackEntityManager(
            $invite->getAccountId(),
            $accountChannel->getId(),
            $customer,
            $request->get('feedback'),
            $request->get('rating'),
            new FeedbackEntity()
        );

        $feedbackEntity = $feedbackEntityManager->createEntity();

        if (($accountChannel->getRatingsEnabled() == '1') && (is_null($feedbackEntity->getRating()))) {
            return $this->showFeedbackForm(
                $request->get('token'),
                $accountChannel,
                $request->get('feedback'),
                'Please select star rating'
            );
        }

        $em->persist($feedbackEntity);
        $em->remove($invite);
        $em->flush();

        $this->get('mfb_email.sender')->sendFeedbackNotification(
            $account,
            $customer,
            $feedbackEntity
        );

        $templateManager = new TemplateManager();
        $templateEntity = $templateManager->getTemplate(
            $invite->getAccountId(),
            $templateManager::THANKYOU_TEMPLATE_TYPE,
            'ThankYouPage',
            $em,
            $this->get('translator')
        );

        $template = new ThankYouTemplate();
        $templateText = $template
            ->setContent($templateEntity->getTemplateCode())
            ->setCustomer($customer)
            ->getTranslation();

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
}
