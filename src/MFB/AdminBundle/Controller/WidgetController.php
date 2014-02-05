<?php

namespace MFB\AdminBundle\Controller;

use MFB\ChannelBundle\Entity\AccountChannel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\WidgetBundle\Entity\Widget as WidgetEntity;
use MFB\WidgetBundle\Form\WidgetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WidgetController extends Controller
{
    /**
     * @Route("/widget", name="mfb_widget")
     * @Template
     */
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

        $base = $request->getSchemeAndHttpHost();
        $lightIconLink = $base.$this->get('templating.helper.assets')
                ->getUrl('bundles/meinfeedbackhome/images/mf_light_de.png');

        $darkIconLink = $base.$this->get('templating.helper.assets')
                ->getUrl('bundles/meinfeedbackhome/images/mf_dark_de.png');

        return array(
                'widgetLink' => $this->getRouteUrl($accountId, 'mfb_account_profile_homepage'),
                'widgetImage' => $this->getRouteUrl($accountId, 'mfb_widget_account_channel'),
                'inviteUrl' => $this->getRouteUrl($accountId, 'mfb_feedback_create'),
                'darkIcon' => $darkIconLink,
                'lightIcon' => $lightIconLink,
                'form' => $form->createView()
        );
    }

    /**
     * @Route("/enableFeedback/{feedbackId}", name="mfb_feedback_enable")
     * @Method({"GET"})
     */

    public function enableAction($feedbackId)
    {
        $this->get('mfb_feedback.service')->activateFeedback($feedbackId);

        $message = $this->get('translator')->trans(
            'Feedback %feedback% was activated',
            array('%feedback%' => $feedbackId)
        );


        $this->getRequest()->getSession()->getFlashBag()->add('success', $message);

        return $this->redirect(
            $this->generateUrl('mfb_admin_homepage')
        );
    }

    /**
     * @Route("/feedback_sort", name="mfb_feedback_sort")
     * @Method({"POST"})
     */
    public function sortAction(Request $request)
    {
        $accountId = $this->getUserId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        $channelFeedbacks = $this->get('mfb_feedback_display.service')->getChannelFeedbacks($channel->getId());
        $channelFeedbacks->setElementsPerPage($this->container->getParameter('mfb_feedback.maxFeedbacks'));

        $feedbackSummaryList = $channelFeedbacks->getFeedbackSummary()->getItems();
        $item_order_str = $request->request->get('item_order_str');
        parse_str($item_order_str, $output);

        foreach ($feedbackSummaryList as $feedbackSummary) {
            $item = $feedbackSummary->getFeedback();
            /** @var Feedback $item */
            $item->setSort(array_search($item->getId(), $output['item_order']));
            $this->get('mfb_feedback.service')->store($item);
        }

        return $this->sortResponse($item_order_str);
    }

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

    private function getUserId()
    {
        return $this->get('security.context')->getToken()->getUser()->getId();
    }

    private function sortResponse($item_order_str)
    {
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

    private function getRouteUrl($accountId, $route)
    {
        $link = $this->generateUrl(
            $route,
            array('accountId' => $accountId),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $link;
    }


}
