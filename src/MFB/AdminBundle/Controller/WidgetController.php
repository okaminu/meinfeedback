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

    public function sortAction(Request $request)
    {
        $accountId = $this->getUserId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        $feedbackList = $this->get('mfb_feedback_display.service')->getFeedbackList($channel->getId());

        $item_order_str = $request->request->get('item_order_str');
        parse_str($item_order_str, $output);

        foreach ($feedbackList as $item) {
            /** @var Feedback $item */
            $item->setSort(array_search($item->getId(), $output['item_order']));
            $this->get('mfb_feedback.service')->store($item);
        }

        return $this->sortResponse($item_order_str);
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

    /**
     * @return mixed
     */
    private function getUserId()
    {
        return $this->get('security.context')->getToken()->getUser()->getId();
    }

    /**
     * @param $item_order_str
     * @return Response
     */
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


}
