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

class WidgetController extends Controller
{
    public function indexAction()
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

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

        $testWidgetLink = $this->generateUrl(
            'mfb_account_profile_homepage',
            array('accountId' => 12),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $testWidgetImage = $this->generateUrl(
            'mfb_widget_account_channel',
            array('accountId' => 12),
            UrlGeneratorInterface::ABSOLUTE_URL
        );


        /* @todo  this is not clear. should we use url from invite */
        $inviteUrl  = $this->generateUrl(
            'mfb_feedback_create_with_invite',
            array('token' => '87b074b234cace56f95efaea9bd9f50f'),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->render(
            'MFBAdminBundle:Widget:index.html.twig',
            array(
                'widgetLink' => $widgetLink,
                'widgetImage' => $widgetImage,
                'testWidgetLink' => $testWidgetLink,
                'testWidgetImage' => $testWidgetImage,
                'inviteUrl' => $inviteUrl
            )
        );
    }
}
