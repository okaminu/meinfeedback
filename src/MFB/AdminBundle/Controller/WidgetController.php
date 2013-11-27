<?php

namespace MFB\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\WidgetBundle\Entity\Widget as WidgetEntity;
use MFB\WidgetBundle\Form\WidgetType;

class WidgetController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $account = $em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exits');
        }
        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$account->getId())
        );
        if (!$accountChannel) {
            throw $this->createNotFoundException('No feedback yet. Sorry.');
        }

        $widget = $em->getRepository('MFBWidgetBundle:Widget')->findOneBy(
            array('accountId' => $account->getId(), 'channelId' => $accountChannel->getId())
        );

        if (!$widget) {
            $widget = $this->createDefaultWidget($account, $accountChannel, $em);
        }

        $form = $this->createForm(new WidgetType(), $widget, array(
                'action' => $this->generateUrl('mfb_widget'),
                'method' => 'POST',
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($widget);
            $em->flush();
        }


        $widgetLink = $this->generateUrl(
            'mfb_account_profile_homepage',
            array('accountId' => $accountId),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $widgetImage = $this->generateUrl(
            'mfb_widget_account_channel',
            array('accountId' => $accountId),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $inviteUrl  = $this->generateUrl(
            'mfb_feedback_create',
            array('accountId' => $accountId),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $base = $request->getSchemeAndHttpHost();
        $lightIconLink = $base.$this->get('templating.helper.assets')
                ->getUrl('bundles/meinfeedbackhome/images/mf_light_de.png');

        $darkIconLink = $base.$this->get('templating.helper.assets')
                ->getUrl('bundles/meinfeedbackhome/images/mf_dark_de.png');

        return $this->render(
            'MFBAdminBundle:Widget:index.html.twig',
            array(
                'widgetLink' => $widgetLink,
                'widgetImage' => $widgetImage,
                'inviteUrl' => $inviteUrl,
                'darkIcon' => $darkIconLink,
                'lightIcon' => $lightIconLink,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Enable Feedback by feedback link
     *
     * @param $feedbackId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function enableAction($feedbackId)
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        try {
            $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('MFBFeedbackBundle:Feedback')
                ->activateFeedback($feedbackId, $accountId);

        } catch (NoResultException $e) {
            throw $this->createNotFoundException('Feedback was not found.');
        }

        $message = $this->get('translator')->trans(
            'Feedback %feedback% was activated',
            array('%feedback%' => $feedbackId)
        );

        $this->getRequest()->getSession()->getFlashBag()->add('success', $message);

        return $this->redirect(
            $this->generateUrl('mfb_admin_homepage')
        );
    }

    public function sortAction(Request $request)
    {
        $accountId = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $feedbackList = $em
            ->getRepository('MFBFeedbackBundle:Feedback')
            ->findSortedByAccountId($accountId);

        $item_order_str = $request->request->get('item_order_str');
        parse_str($item_order_str, $output);

        foreach ($feedbackList as $item) {
            /** @var Feedback $item */
            $item->setSort(array_search($item->getId(), $output['item_order']));
            $em->persist($item);
            $em->flush();
        }

        $response = new Response();
        $response->setContent(
            json_encode(
                array(
                    'success' => true,
                    'sort' => $item_order_str
                )
            )
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param $account
     * @param $accountChannel
     * @param $em
     * @return WidgetEntity
     */
    public function createDefaultWidget($account, $accountChannel, $em)
    {
        $widget = new WidgetEntity();
        $widget->setAccountId($account->getId());
        $widget->setChannelId($accountChannel->getId());
        $widget->setTextColorCode('6c6c6c');
        $widget->setBackgroundColorCode('5AFF6A');
        $em->persist($widget);
        $em->flush();
        return $widget;
    }


}
